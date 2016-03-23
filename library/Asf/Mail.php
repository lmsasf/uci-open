<?php

require_once 'pcg_od_mailModel.php';
require_once 'pcg_om_modulo_mailModel.php';

class Asf_Mail 
{
    private $_sUser ;

    public function __construct()
    {
        // # Identificamos usuario # //
        $this->_sUser = $_SESSION["username"];
    }

    /**
     * Envía correos con PHP Zend adjuntando archivos y dejando log en tabla OD_MAIL. Crea de manera opcional relación entre email y objeto
     * en la tabla
     * @param str $subject, str $body, str $from, str $to, str $bcc, int $modulo, str $objects, array $arguments, array $attachments, bool $noLog
     * @return bolean status
     */
    public function sendMail($subject, $body, $from, $to, $bcc = null, $modulo, $objects = null, $arguments = null, $attachments = null, $noLog = false)
    {
        $adjuntos = '';
        $mails = new pcg_od_mailModel();
        $email = $mails->createRow();
        $email->DESTINO = $to;
        $email->ASUNTO  = $subject;
        $email->DESDE   = $from;
        $email->FECHA   = date("d/m/Y H:i:s");
        $email->MODULO  = $modulo;
        $email->USUARIO = $this->_sUser;
        $email->CC      = $bcc;

        try
        {
            if($this->isValidEmail($to))
            {
                if(is_array($arguments))
                {
                    // Get the template arguments //
                    $template  = $arguments['template'];
                    $templatearguments = $arguments['templateArguments'];

                    // Build a view //
                    $view = new Zend_View;
                    $rootPath = dirname(dirname(__FILE__));
                    $view->setScriptPath(realpath('../application/modules/default/views/scripts/mailtemplates/'));

                    // Assign each item //
                    foreach($templatearguments as $k => $v)
                    {
                        $view->assign($k, $v);
                    }

                    // Render the template //
                    $body = $view->render($template);
                }

                $zend_mail = new Zend_Mail();
                $zend_mail->setBodyHtml($body);
                $zend_mail->setFrom($from);
                $zend_mail->setReplyTo($from);
                $zend_mail->addTo($to);
                $zend_mail->setSubject($subject);

                if ($bcc) { $zend_mail->addBcc($bcc); }

                if(is_array($attachments))
                {
                    $adjuntos = implode(',', $attachments);
                    foreach($attachments as $attachment)
                    {
                        $fileContents = file_get_contents($attachment);
                        $attach = $zend_mail->createAttachment($fileContents);
                        $attach->filename = basename($attachment);
                    }
                }
                if($zend_mail->send())
                {
                    $email->ESTADO  = 1;
                }
                else
                {
                    $email->ESTADO  = 2;
                }
            }
            else
            {
                $email->ESTADO  = 3;
            }
        }
        catch (Exception $e)
        {
            $email->ESTADO  = 2;
        }

        $email->CUERPO  = $body;

        // Guarda log de correo en BD y objetos en tabla relacional //
        if(!$noLog)
        {
            try
            {
                $id_mail = $email->save();
                $modulo_mail = new pcg_om_modulo_mailModel();
                $objects = explode(",",$objects);

                foreach($objects as $k => $value)
                {
                    $module = $modulo_mail->createRow();
                    $module->ID_MAIL    = $id_mail;
                    $module->ID_MODULO  = $modulo;
                    $module->ID_OBJECT  = $value;
                    $module->save();
                }
            }
            catch (Exception $e)
            {
                return 4;
            }
        }

        return $email->ESTADO;
    }

    /**
     * Determina si una o muchas direcciones de email son válidas
     * @param string $email
     * @return bolean
     */
    public static function isValidEmail($email)
    {
        $validator  = new Zend_Validate_EmailAddress();
        $address    = explode(";",$email);

        foreach($address as $k => $value)
        {
            if(!$validator->isValid($value)) { return false; }
        }

        return true;
    }
}

?>