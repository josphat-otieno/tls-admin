<?php
error_reporting(E_ALL);

require_once("../../vendor/autoload.php"); 
use Mailgun\Mailgun;

$newstaff_id='34464646';

$compname="Usalama";
$compemail="donotreply@usalama.app";

$to="eastherao1@gmail.com";
$to_name="Easther Akinyi";

//   $to  = $ceo_email;
$subject = 'Your Login Credentials';

$message = "";
//$application_date = gmdate('j l, F Y',$application_date);
$body1 = "<head>
<title>Activate Account</title>
<meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\">
<meta content=\"width=device-width\" name=\"viewport\">
<style type=\"text/css\">
            @font-face {
              font-family: &#x27;Postmates Std&#x27;;
              font-weight: 600;
              font-style: normal;
              src: local(&#x27;Postmates Std Bold&#x27;), url(https://s3-us-west-1.amazonaws.com/buyer-static.postmates.com/assets/email/postmates-std-bold.woff) format(&#x27;woff&#x27;);
            }

            @font-face {
              font-family: &#x27;Postmates Std&#x27;;
              font-weight: 500;
              font-style: normal;
              src: local(&#x27;Postmates Std Medium&#x27;), url(https://s3-us-west-1.amazonaws.com/buyer-static.postmates.com/assets/email/postmates-std-medium.woff) format(&#x27;woff&#x27;);
            }

            @font-face {
              font-family: &#x27;Postmates Std&#x27;;
              font-weight: 400;
              font-style: normal;
              src: local(&#x27;Postmates Std Regular&#x27;), url(https://s3-us-west-1.amazonaws.com/buyer-static.postmates.com/assets/email/postmates-std-regular.woff) format(&#x27;woff&#x27;);
            }


            @font-face {
                font-family: 'Century Gothic';
                src: url('https://usalama.app/CenturyGothic-Bold.woff2') format('woff2'),
                    url('https://usalama.app/CenturyGothic-Bold.woff') format('woff');
                font-weight: bold;
                font-style: normal;
                font-display: swap;
            }

        </style>
<style media=\"screen and (max-width: 680px)\">
            @media screen and (max-width: 680px) {
                .page-center {
                  padding-left: 0 !important;
                  padding-right: 0 !important;
                }
                
                .footer-center {
                  padding-left: 20px !important;
                  padding-right: 20px !important;
                }
            }
        </style>
</head>
<body style=\"background-color: #f4f4f5;\">
<table cellpadding=\"0\" cellspacing=\"0\" style=\"width: 100%; height: 100%; background-color: #f4f4f5; text-align: center;\">
<tbody><tr>
<td style=\"text-align: center;\">
<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" id=\"body\" style=\"background-color: #fff; width: 100%; max-width: 680px; height: 100%;\">
<tbody><tr>
<td>
<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" class=\"page-center\" style=\"text-align: left; padding-bottom: 88px; width: 100%; padding-left: 120px; padding-right: 120px;\">
<tbody><tr>
<td style=\"padding-top: 24px;\">
<img src=\"https://usalama.app/android/client/usalama_logo_red_large.png\" style=\"width: 72px;\">
</td>
</tr>
<tr>
<td colspan=\"2\" style=\"padding-top: 72px; -ms-text-size-adjust: 100%; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; color: #000000; font-family: 'Century Gothic','Postmates Std', 'Helvetica', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif; font-size: 48px; font-smoothing: always; font-style: normal; font-weight: 600; letter-spacing: -2.6px; line-height: 52px; mso-line-height-rule: exactly; text-decoration: none;\">jaybeibz</td>
</tr>
<tr>
<td style=\"padding-top: 48px; padding-bottom: 48px;\">
<table cellpadding=\"0\" cellspacing=\"0\" style=\"width: 100%\">
<tbody><tr>
<td style=\"width: 100%; height: 1px; max-height: 1px; background-color: #d9dbe0; opacity: 0.81\"></td>
</tr>
</tbody></table>
</td>
</tr>
<tr>
<td style=\"-ms-text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; color: #9095a2; font-family:'Century Gothic', 'Postmates Std', 'Helvetica', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif; font-size: 16px; font-smoothing: always; font-style: normal; font-weight: 400; letter-spacing: -0.18px; line-height: 24px; mso-line-height-rule: exactly; text-decoration: none; vertical-align: top; width: 100%;\">
                                      You're receiving this e-mail because have been added as an editor on Usalama Loan Admin. Please use the PASSWORD above and YOUR EMAIL address to log into your account.
                                    </td>
</tr>
<tr>
<td style=\"padding-top: 24px; -ms-text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; color: #9095a2; font-family: 'Century Gothic','Postmates Std', 'Helvetica', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif; font-size: 16px; font-smoothing: always; font-style: normal; font-weight: 400; letter-spacing: -0.18px; line-height: 24px; mso-line-height-rule: exactly; text-decoration: none; vertical-align: top; width: 100%;\">
                                      Please tap link below to go to the site.
                                    </td>
</tr>
<tr>
<td>
<a data-click-track-id=\"37\" href=\"https://usalama.app/loans/admin_loan/login.php\" style=\"margin-top: 36px; -ms-text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; color: #ffffff; font-family: 'Century Gothic','Postmates Std', 'Helvetica', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif; font-size: 12px; font-smoothing: always; font-style: normal; font-weight: 600; letter-spacing: 0.7px; line-height: 48px; mso-line-height-rule: exactly; text-decoration: none; vertical-align: top; width: 220px; background-color: #E82949; border-radius: 28px; display: block; text-align: center; text-transform: uppercase\" target=\"_blank\">
                                        Open Link
                                      </a>
</td>
</tr>
</tbody></table>
</td>
</tr>
</tbody></table>
<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" id=\"footer\" style=\"background-color: #000; width: 100%; max-width: 680px; height: 100%;\">
<tbody><tr>
<td>
<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" class=\"footer-center\" style=\"text-align: left; width: 100%; padding-left: 120px; padding-right: 120px;\">
<tbody><tr>
<td colspan=\"2\" style=\"padding-top: 72px; padding-bottom: 24px; width: 100%;\">
<img src=\"https://usalama.app/android/client/usalama_white.png\" style=\"width: 117px; height: 11px\">
</td>
</tr>
<tr>
<td colspan=\"2\" style=\"padding-top: 24px; padding-bottom: 48px;\">
<table cellpadding=\"0\" cellspacing=\"0\" style=\"width: 100%\">
<tbody><tr>
<td style=\"width: 100%; height: 1px; max-height: 1px; background-color: #EAECF2; opacity: 0.19\"></td>
</tr>
</tbody></table>
</td>
</tr>
<tr>
<td style=\"-ms-text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; color: #9095A2; font-family: 'Century Gothic','Postmates Std', 'Helvetica', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif; font-size: 15px; font-smoothing: always; font-style: normal; font-weight: 400; letter-spacing: 0; line-height: 24px; mso-line-height-rule: exactly; text-decoration: none; vertical-align: top; width: 100%;\">
                                          If you have any questions or concerns, we're here to help. Contact us via our <a data-click-track-id=\"1053\" href=\"https://support.usalama.app\" style=\"font-weight: 500; color: #ffffff\" target=\"_blank\">Help Center</a>.
                                        </td>
</tr>
<tr>
<td style=\"height: 72px;\"></td>
</tr>
</tbody></table>
</td>
</tr>
</tbody></table>
</td>
</tr>
</tbody></table>



</body>";
// $emailbody = $body1;

// # Instantiate the client.
// $mgClient = new Mailgun('-0f472795-88d0b078');
// $domain = "https://api.mailgun.net/v3/mail.usalama.app";
// $result = $mgClient->sendMessage($domain, array(
// 	'from'	=> $compname .'<'.$compemail.'>',
// 	'to'	=> $to_name .'<'.$to.'>',
// 	'subject' => $subject,
// 	'html'	=> $emailbody
// ));

