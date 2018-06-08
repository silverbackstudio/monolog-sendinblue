<?php
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Svbk\Monolog\Sendinblue;

use Monolog\Logger;
use Monolog\Handler\MailHandler;
use Monolog\Handler\Curl;

/**
 * SendinblueHandler uses the SendinBblue API v3 function to send Log emails, more information in https://developers.sendinblue.com/v3.0/reference#sendtransacemail-1
 *
 * @author Brando Meniconi <b.meniconi@silverbackstudio.it>
 */
class Handler extends MailHandler
{
    /**
     * The Sendinblue API Key.
     * @var string
     */
    protected $apiKey;
    
    /**
     * The email addresses to which the message will be sent
     * @var string
     */
    protected $from;
    
    /**
     * The email addresses to which the message will be sent
     * @var array
     */
    protected $to;
    
    /**
     * The subject of the email
     * @var string
     */
    protected $subject;
    
    /**
     * @param string       $apiKey  The Sendinblue API v3 Key
     * @param string       $from    The sender of the email
     * @param string|array $to      The recipients of the email
     * @param string       $subject The subject of the mail
     * @param int          $level   The minimum logging level at which this handler will be triggered
     * @param bool         $bubble  Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct( $apiKey, $from, $to, $subject, $level = Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->apiKey = $apiKey;
        $this->from = $from;
        $this->to = (array) $to;
        $this->subject = $subject;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function send( $content, array $records )
    {
        $message = [];
        
        $message['sender'] = array(
            'email' => $this->from
        );
        foreach ($this->to as $recipient) {
            $message['to'][] = array(
                'email' => $recipient
            );
        }
        $message['subject'] = $this->subject;
        $message['date'] = date('r');

        $message['htmlContent'] = $content;

        // if ( ! $this->isHtmlBody($content) ) {
        //     $message['textContent'] = $content;
        // }

        $message['tags'] = array( 'log' );

        $json_message = json_encode( $message );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'api-key: ' . $this->apiKey,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_message)
        ));

        curl_setopt($ch, CURLOPT_URL, 'https://api.sendinblue.com/v3/smtp/email');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_message);

        Curl\Util::execute($ch, 2);
    }
}