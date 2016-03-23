<?php
/**
 * Clase que mapea a la tabla Category
 * @author damills
 */
class Table_Category extends Zend_Db_Table_Abstract {

    protected $_name    = 'Category';
    protected $_primary = 'id';
    private $posicion = 0;

    /**
     * Obtiene el arbol completo con sus niveles
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getTree(){		
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('n'=>$this->_name), array('n.id', 'n.catName', new Zend_Db_Expr('COUNT(*)-1 AS level') ) )
                        ->from(array('p'=>$this->_name), '')
                        ->where('n.lft BETWEEN p.lft AND p.rgt')
                        ->group('n.lft')
                        ->order('n.posicion')
        ;		
        return $this->fetchAll($select);
    }
	
    public function getTreeWithPaths(){
        $sql="SELECT    
                                  n.id
                                , n.catName
                                , COUNT(*)-1 AS level
                                , (SELECT GROUP_CONCAT(DISTINCT parent.catName ORDER BY (parent.rgt - parent.lft) desc SEPARATOR ' ► ')
                                        FROM Category AS node,Category AS parent
                                                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                          AND node.id = n.id group by node.id) AS path
                        FROM Category AS n, Category AS p
                WHERE n.lft BETWEEN p.lft AND p.rgt  
                GROUP BY n.lft 
                ORDER BY n.posicion";
        return $this->getAdapter()->fetchAll($sql);

    }
    
    /**
     * Obtiene el path de una categoría
     * @param integer $id
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    function getPath($id = null){
        if(is_null($id)){
            throw new Exception('id is null');
        }
        $sql="SELECT    
                        n.id
                      , n.catName
                      , COUNT(*)-1 AS level
                      , (SELECT GROUP_CONCAT(parent.catName ORDER BY (parent.rgt - parent.lft) desc SEPARATOR ' ► ')
                              FROM Category AS node,Category AS parent
                                      WHERE node.lft BETWEEN parent.lft AND parent.rgt
                        AND node.id = n.id group by node.id ) AS path
                        FROM Category AS n, Category AS p
                WHERE n.lft BETWEEN p.lft AND p.rgt and n.id = $id
                GROUP BY n.lft 
                ORDER BY n.posicion";		 
        return $this->getAdapter()->fetchAll($sql);
    }
	
    /**
     * Get the HTML code for an unordered list of the tree
     * @return string HTML code for an unordered list of the whole tree
     */
    public function treeAsHtml() {
        $dt = $this->getTree()->toArray();
        $tree = array();
        $i = 0;
        foreach ($dt As $row) {
            $tree[$i] = $row;
            $i++;
        }
        $html = "<ol class=\"dd-list\">\n";
        for ($i=0; $i<count($tree); $i++) {
            $buttonEdit='<a class="smallButton" title="Edit" style="margin: 0px; margin-right:3px; float:right;height: 16px;" title="" href="javascript:edit('.$tree[$i]['id'].')"><img alt="" src="/backend/images/icons/dark/pencil.png"></a>';

            $buttonDel ='<a class="smallButton deleteCat" title="Delete" style="margin: 0px; margin-right:3px; float:right;height: 16px;" title="" href="javascript:del('.$tree[$i]['id'].')"><img alt="" src="/backend/images/icons/dark/close.png"></a>';

            $html .= "<li class=\"dd-item dd3-item\" data-id=\"".$tree[$i]['id']."\"><div class=\"dd-handle dd3-handle\"></div><div class=\"dd3-content\">" . $tree[$i]['catName']. $buttonDel . $buttonEdit  ."</div>";
            if ($tree[$i]['level'] < @$tree[$i+1]['level']) {
                $html .= "\n<ol class=\"dd-list\">\n";
            } elseif ($tree[$i]['level'] == @$tree[$i+1]['level']) {
                $html .= "</li>\n";
            } else {
                $diff = $tree[$i]['level'] - (isset($tree[$i+1]) ? $tree[$i+1]['level']: 0 );
                $html .= str_repeat("</li>\n</ol>\n", $diff) . "</li>\n";
            }
        }
        $html .= "</ol>\n";
        return $html;
    }
        
    /**
     * Devuelve el arbol pero con solo dos niveles
     * @return Array
     */
    public function treeAsArray($idType){
        try {
            if(is_null($idType) ){
                throw new Exception('invalid type');
            }
            $padres = $this->getParents($idType);
            $tree = array();
            foreach ($padres as $padre) {
                $tree[$padre['id']]['node'] = $padre;
                $tree[$padre['id']]['childrens']= $this->getChildrens($padre['id'], $idType);
            }                        
            return $tree;
        } catch (Exception $e ){
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Obtiene los nodos padres
     * @return Array
     */
    private function getParents($idType){
        $sql="SELECT parent.id, parent.catName, (COUNT(parent.catName) - 1) AS depth
            FROM Category AS node,
                            Category AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt AND (SELECT COUNT(*) FROM OCWCategory r0 INNER JOIN OCW r1 ON r0.idOCW = r1.id WHERE node.id = r0.idCat AND r1.ocwGolive =1 AND r1.idType=$idType) > 0
            GROUP BY node.id
            HAVING depth BETWEEN 0 AND 2
        ORDER BY node.lft";
        return $this->getAdapter()->fetchAll($sql);		
    }
    
    /**
     * Obtiene los nodos hijos
     * @param integer $idNode
     * @return Array
     */
    private function getChildrens($idNode, $idType){
        $sql="SELECT node.id, node.catName, (COUNT(parent.catName) - (sub_tree.depth + 1)) AS depth
                        FROM Category AS node,
                                Category AS parent,
                                Category AS sub_parent,
                                (
                                        SELECT node.catName, (COUNT(parent.catName) - 1) AS depth
                                        FROM Category AS node,
                                                Category AS parent
                                        WHERE node.lft BETWEEN parent.lft AND parent.rgt
                                                AND node.id = $idNode
                                        GROUP BY node.catName
                                        ORDER BY node.lft
                                )AS sub_tree
                        WHERE node.lft BETWEEN parent.lft AND parent.rgt
                                AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
                                AND sub_parent.catName = sub_tree.catName
                                AND (SELECT COUNT(*) FROM OCWCategory r0 INNER JOIN OCW r1 ON r0.idOCW = r1.id WHERE node.id = r0.idCat AND r1.ocwGolive =1 AND r1.idType=$idType) > 0
                        GROUP BY node.id
                        HAVING depth = 1
                        ORDER BY node.lft";  
        return $this->getAdapter()->fetchAll($sql);
    }
    
    /**
     * Agrega una categoria nueva
     * @param string $name Nombre de la categoria
     * @param number $parentId opcional si se omite se agrega como categoria padre
     */
    public function addCategory( $name, $parentId = 0 ){
        try {
            if(is_null($name) || is_null($parentId)){
                throw new Exception('Invalid parameter');
            }
            $sql = "CALL SPAddCategory($parentId, '$name');";
            $ret = $this->getAdapter()->fetchRow($sql);
            if(!empty($ret)){
                return $this->getAdapter()->fetchRow('SELECT LAST_INSERT_ID() as id');
            }else{
                throw new Exception('Failed to add category');
            }
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Mueve una categoria, este método es publico pero se usa
     * para reconstruir el arbol y no para mover individualmente
     * @param number $idCat
     * @param number $parentId
     * @param number $posicion
     * @throws Exception
     * @return Ambigous <multitype:, mixed>
     */
    public function moveCategory( $idCat, $parentId = 0, $posicion  ){
        if(is_null($idCat) || is_null($parentId)){
            throw new Exception('Invalid parameter');
        }
        $sql = "CALL SPMoveCategory($idCat, $parentId, $posicion);";
        $ret = $this->getAdapter()->fetchRow($sql);
        if(!empty($ret)){
            return $this->getAdapter()->fetchRow('SELECT LAST_INSERT_ID() as id');
        }else{
            throw new Exception('Failed to move category');
        }
    }
    
    /**
     * Borra una categoria
     * @param number $idCat
     * @throws Exception
     * @return integer
     */	
    public function delCategory( $idCat){
        if(is_null($idCat)){
            throw new Exception('Invalid parameter');
        }
        $select = $this->select();
        // SELECT @myLeft := lft, @myRight := rgt, @myWidth := rgt - lft + 1 FROM Category WHERE id = idCat;
        $select->from('Category', array('lft', 'rgt', new Zend_Db_Expr('(rgt - lft + 1) as width')))
                   ->where('id = ?', $idCat);
        //$sql = "CALL SPDelCategory($idCat);";
        //$ret = $this->getAdapter()->fetchRow($sql);
        $category = $this->fetchRow($select);
        $db = $this->getAdapter();
        $tr = $db->beginTransaction();
        try {
            //DELETE FROM Category WHERE lft BETWEEN @myLeft AND @myRight;
            $tr->delete('Category', 'lft BETWEEN ' . $category->lft . ' AND ' . $category->rgt );
            // UPDATE Category SET rgt = rgt - @myWidth WHERE rgt > @myRight;
            $tr->update('Category', array('rgt'=> new Zend_Db_Expr('(rgt - '. $category->width .')') ), 'rgt > ' . $category->rgt );
            // UPDATE Category SET lft = lft - @myWidth WHERE lft > @myRight;
            $tr->update('Category', array('lft'=> new Zend_Db_Expr('(lft - '. $category->width .')') ), 'lft > ' . $category->rgt );
            $tr->commit();
            return $this->getAdapter()->fetchRow('SELECT LAST_INSERT_ID() as id');
        } catch (Exception $e) {
            $tr->rollBack();
            throw new Exception('The category or sub-category is related to ocw. ');
        }
    }
    
    /**
     * Regenera el arbol 
     * @param array $data este array es lo que me devuelve el componente nestable
     * @return boolean
     */
    public function rebuildTree($data){
        // borro los lft y rgy de la tabla
        $this->update(array( 'lft'=>0, 'rgt'=>0 ), '1=1');
        //recorro los nodos padres
        $this->posicion = 0;
        foreach ($data as $parents ){
            //inserto el padre
            //d($parents['children']); exit;
            $idCat = $parents['id'];
            $posicion = $this->posicion++;
            $ret = $this->moveCategory($idCat, 0, $posicion);
            //verifico si este parent tiene hijos

            if(array_key_exists('children', $parents) ){
                $this->addChild($idCat, $parents['children']);
            }
        }
        return true;
    }
    
    /**
     * Funcion auxiliar para reconstruir el arbol, se llama recursivamente para
     * actualizar los hijos de cada rama
     * @param integer $idParent
     * @param integer $children
     */
    private function addChild($idParent, $children ){
        foreach ($children as $child){
            $idCat = (int)$child['id'];
            $posicion = $this->posicion++;
            $ret = $this->moveCategory($idCat, $idParent, $posicion);
       
            if(array_key_exists('children', $child) ){
                $this->addChild($idCat, $child['children']);
            }
        }
    }
        
        
    /**
     * Funcion que retorna los id de subcategorias de una Categoria
     * @param array $idCategories
     * @return array
     */        
    public function getSubCategory($idCategories){
        try{
            $in_cat = $idCategories;
            if(is_array($idCategories)){
                $in_cat = implode(',', $idCategories);
            }

            # Trae los id de las subcategorias incluyendo el id de la categoria,
            # pero si ingresamos un id de SubCategoria devuelve vacio
            $sql = "select distinct id from (
                                    SELECT n.id
                                    , (SELECT parent.id
                                    FROM Category AS node,Category AS parent
                                    WHERE node.lft BETWEEN parent.lft AND parent.rgt
                                    AND node.id = n.id group by node.id) AS parentID
                                    FROM Category AS n, Category AS p
                                    WHERE n.lft BETWEEN p.lft AND p.rgt
                                    GROUP BY n.lft) a where a.parentID in ($in_cat)";

            $consulta = $this->getDefaultAdapter()->fetchAll($sql);

            if(empty($consulta)){
                $res = $in_cat;
            }
            else{
                $ids = [];
                foreach ($consulta as $val){
                    $ids[] = $val['id'];
                }
                if(is_array($idCategories)){
                    foreach ($idCategories as $value) {
                        if(in_array($value, $ids) == false){
                            $ids[] = $value;
                        }
                    }
                }else{
                    if(in_array($idCategories, $ids) == false){
                        $ids[] = $idCategories;
                    }
                }
                $res = $ids;
            }

            return $res;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *  Funcion que retorna los id de Categorias para un TipoOCW
     * @param String $id_type
     * @return array
     */  
    public function getTreeCategory($id_type) {
        $id_type = intval($id_type);

        $sql="SELECT
                        n.id
                        , n.catName
                        , COUNT(*)-1 AS level
                        , (SELECT GROUP_CONCAT(distinct parent.catName ORDER BY (parent.rgt - parent.lft) desc SEPARATOR ' ► ')
                            FROM Category AS node,Category AS parent
                                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                        AND node.id = n.id group by node.id ) AS path
                        FROM Category AS n, Category AS p
                        WHERE n.lft BETWEEN p.lft AND p.rgt and n.id in (select id from Category where id in (
                            SELECT idCat FROM ocw.OCWCategory where idOCW in (
                            SELECT id FROM ocw.OCW where idType in (SELECT id FROM ocw.OCWTypes where id in ($id_type)))))
                    GROUP BY n.lft
                    ORDER BY n.posicion";

        $resInclude = $this->getAdapter()->fetchAll($sql);

        $sql="SELECT
                        n.id
                        , n.catName
                        , COUNT(*)-1 AS level
                        , (SELECT GROUP_CONCAT(distinct parent.catName ORDER BY (parent.rgt - parent.lft) desc SEPARATOR ' ► ')
                            FROM Category AS node,Category AS parent
                                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                        AND node.id = n.id group by node.id ) AS path
                        FROM Category AS n, Category AS p
                        WHERE n.lft BETWEEN p.lft AND p.rgt
                    GROUP BY n.lft
                    ORDER BY n.posicion";

        $res = $this->getAdapter()->fetchAll($sql);

        return array($resInclude,$res);
    }
    
    /**
     * Funcion que retorna los registros de Categorias para unos id de Categorias especificos
     * @param array $id_category
     * @return array
     */  
    public function getCategoryEdit($id_category) {            
        $sql="SELECT    
                                      n.id
                                    , n.catName
                                    , COUNT(*)-1 AS level
                                    , (SELECT GROUP_CONCAT(DISTINCT parent.catName ORDER BY (parent.rgt - parent.lft) desc SEPARATOR ' ► ')
                                            FROM Category AS node,Category AS parent
                                                    WHERE node.lft BETWEEN parent.lft AND parent.rgt
                              AND node.id = n.id group by node.id) AS path
                            FROM Category AS n, Category AS p
                    WHERE n.lft BETWEEN p.lft AND p.rgt  and n.id in (".$id_category.")
                    GROUP BY n.lft 
                    ORDER BY n.posicion";        

        $res = $this->getAdapter()->fetchAll($sql);
        return $res;
    }
    
    public function getCategoriesForAds($id_page) {
        $category_ads = $this   ->select()
                                ->setIntegrityCheck(false)
                                ->from(array('c0'=>'Category'),array('c0.id'))
                                ->joinInner(array('c1'=>'OCWCategory'), 'c0.id = c1.idCat', array())
                                ->joinInner(array('c2'=>'OCW'), 'c1.idOCW = c2.id', array())
                                ->where('c2.id = ?', $id_page);
        $cat_ads = $this->fetchAll($category_ads);                            
        
        $id_categoriesForPages = array();
        foreach ($cat_ads as $value){
            $id_categoriesForPages[] = $value['id'];            
        }
        
        $id_categories = array_unique($id_categoriesForPages);
                        
        $result_categories = $this->getSubCategory($id_categories);
        
        $ids_categories = array();
        if(is_array($result_categories)){
            $ids_categories = $result_categories;
        }else{
            $ids_categories[] = $result_categories;
        }
        
        $id_categoria_padre = $this->getCategoryForSubCat($id_categoriesForPages);
        
        foreach ($id_categoria_padre as $value) {
            $ids_categories[] = $value;           
        }

        $ids_categories = array_unique($ids_categories);
                
        if(is_array($ids_categories)){
            $id_cat_values = implode(',', $ids_categories);
        }else{
            $id_cat_values = $ids_categories;
        }

        return $id_cat_values;        
    }

    public function getCategoryForSubCat($id_subcategory) {
        
        if(is_array($id_subcategory)){
            $id_subcategory = implode(',', $id_subcategory);
            
            $sql="SELECT p.id
                            FROM Category AS n, Category AS p
                            WHERE n.lft BETWEEN p.lft AND p.rgt AND n.id IN ($id_subcategory)
                            GROUP BY n.lft
                            ORDER BY n.posicion";

            $res = $this->getAdapter()->fetchAll($sql);

            $ids = array();
            foreach($res as $value) {
                $ids[] = $value['id'];
            }

            $ids = array_unique($ids);
            return $ids;
            
        } else {
            $sql="SELECT p.id
                            FROM Category AS n, Category AS p
                            WHERE n.lft BETWEEN p.lft AND p.rgt AND n.id IN ($id_subcategory)
                            GROUP BY n.lft
                            ORDER BY n.posicion";
            $res = $this->getAdapter()->fetchRow($sql);            
            return $res['id'];
        }          
    }       
}
