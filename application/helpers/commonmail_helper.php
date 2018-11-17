<?php
/**
 * Send an email
 *
 * @access  public
 * @return  bool
 */
if ( ! function_exists('send_email'))
{
    function send_fetch_feedback_mail($recipient, $subject, $message, $from_email = NULL, $from_name = NULL, $method = NULL)
    {
        // Obtain a reference to the ci super object
        $CI =& get_instance();
        switch(strtolower($method))
        {
            /*
             * SES Free Tier allows 2000 emails per day (Up to 10,000 per day)
             * see: http://aws.amazon.com/ses/pricing/
             */
            case 'ses':
                $CI->load->library('aws_lib');
                $sender = $from_email ? ($from_name ? $from_name.' <'.$from_email.'>' : $from_email) : NULL;
                $CI->aws_lib->send_email($recipient, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $sender);
            break;

            /*
             * Mandrill Free Tier allows 12,000 per month
             * see: http://mandrill.com/pricing/
             */
            case 'mandrill':
                // todo...
            break;

            default:
                $CI->load->library('email');
                $CI->email->from($from_email, $from_name);
                $CI->email->to($recipient);
                $CI->email->set_mailtype("html");
                $CI->email->subject($subject);
                $CI->email->message($message);
                $CI->email->send();
                log_message('debug', $CI->email->print_debugger());
        }
    }
}

