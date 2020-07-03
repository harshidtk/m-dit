<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$errors = array();
$data = array();
$files = array();
$fields = array(
  "Student Details" => array(
    'name' => array(
      'label' => 'Name',
    ),
    'birthday' => array(
      'label' => 'Date Of Birth',
    ),
    'housename' => array(
      'label' => 'House name',
    ),
    'place' => array(
      'label' => 'Place',
    ),
    'post_office' => array(
      'label' => 'Post office',
    ),
    'pincode' => array(
      'label' => 'Pincode',
    ),
    'state' => array(
      'label' => 'State',
    ),
    'district' => array(
      'label' => 'District',
    ),
    'applicant_mobile' => array(
      'label' => 'Applicant\'s mobile',
    ),
    'father_mother_name' => array(
      'label' => 'Name of father/mother',
    ),
    'father_mother_mobile' => array(
      'label' => 'Mobile number of father/mother',
    ),
  ),
  "Examination 1" => array(
    'examination_1' => array(
      'label' => 'SSLC/THSLC/CBSE',
    ),
    'examination_1_result' => array(
      'label' => 'Passed/Result Waiting',
    ),
    'examination_1_year_of_pass' => array(
      'label' => 'Year Of pass',
    ),
    'examination_1_percentage_of_mark' => array(
      'label' => 'Percentage of Marks',
    ),
    'examination_1_no_of_chances' => array(
      'label' => 'No. of chances',
    ),
    'examination_1_english_grade' => array(
      'label' => 'English grade',
    ),
    'examination_1_physics_grade' => array(
      'label' => 'Physics grade',
    ),
    'examination_1_chemistry_grade' => array(
      'label' => 'Chemistry grade',
    ),
    'examination_1_maths_grade' => array(
      'label' => 'Maths grade',
    ),
  ),
  "Examination 2" => array(
    'examination_2' => array(
      'label' => '+2 Science/+2 Commerce/+2 Humanities/VHSE',
    ),
    'examination_2_result' => array(
      'label' => 'Passed/Result Waiting',
    ),
    'examination_2_period' => array(
      'label' => 'Period',
    ),
    'examination_2_year_of_pass' => array(
      'label' => 'Year Of pass',
    ),
    'examination_2_percentage_of_mark' => array(
      'label' => 'Percentage of Marks',
    ),
    'examination_2_no_of_chances' => array(
      'label' => 'No. of chances',
    ),
    'examination_2_english_grade' => array(
      'label' => 'English grade',
    ),
    'examination_2_physics_grade' => array(
      'label' => 'Physics grade',
    ),
    'examination_2_chemistry_grade' => array(
      'label' => 'Chemistry grade',
    ),
    'examination_2_maths_biology_grade' => array(
      'label' => 'Maths grade',
    ),
  ),
  "Examination 3" => array(
    'examination_3' => array(
      'label' => '+2 Science/+2 Commerce/+2 Humanities/VHSE',
    ),
    'examination_3_result' => array(
      'label' => 'Passed/Result Waiting',
    ),
    'examination_3_group_trade' => array(
      'label' => 'Group/Trade',
    ),
    'examination_3_period' => array(
      'label' => 'Period',
    ),
    'examination_3_year_of_pass' => array(
      'label' => 'Year Of pass',
    ),
    'examination_3_percentage_of_mark' => array(
      'label' => 'Percentage of Marks',
    ),
    'examination_3_no_of_chances' => array(
      'label' => 'No. of chances',
    ),
  ),
  "Other Details" => array(
    'periods_of_breaks' => array(
      'label' => 'Periods of breaks & reasons',
    ),
    'branch_preference_1' => array(
      'label' => 'Branch preference - 1',
    ),
    'branch_preference_2' => array(
      'label' => 'Branch preference - 2',
    ),
    'branch_preference_3' => array(
      'label' => 'Branch preference - 3',
    ),
  ),
);

$allowed_files = array(
  'student_photo' => array(
    'label' => 'Student photo',
    'allowed_types' => array('pdf', 'jpg', 'png', 'jpeg'),
  ),
  'application_fee_payment_slip' => array(
    'label' => 'Application fee payment slip',
    'allowed_types' => array('pdf', 'doc', 'docx', 'jpg', 'png', 'jpeg'),
  ),
);

foreach($fields as $section => $section_fields){
  $data[$section] = array();
  foreach($section_fields as $field_name => $field_data){
    $is_required = (isset($field_data['required']) && $field_data['required']) == true ? true : false;
    $submitted_value =  isset($_POST[$field_name]) ? $_POST[$field_name] : "";
    if($is_required == true && empty($submitted_value)){
      array_push($errors, $field_data['label'] . " - Not valid");
    }else{
      $data[$section][$field_data['label']] = $submitted_value;
    }
  }
}

if (isset($_FILES)) {
  foreach ($allowed_files as $allowed_file_name => $allowed_file_prop) {
    if(isset($_FILES[$allowed_file_name]) && ($_FILES[$allowed_file_name]['tmp_name']) ){
      $filename = $_FILES[$allowed_file_name]['name'];
      $ext = pathinfo($filename, PATHINFO_EXTENSION);
      if (in_array($ext, $allowed_file_prop['allowed_types'])){
        $temp_path = $_FILES[$allowed_file_name]['tmp_name'];
        $file_child = array(
          'file' => $temp_path,
          'name' => $filename,
        );
        array_push($files, $file_child);
      }else{
        array_push($errors, $filename . " - Not valid");
      }
    }
  }
}

if(count($errors) == 0){
  $trigger_email = trigger_email_phpmailer($data, $files);
  //$trigger_email = trigger_email_php($data, $files);
}else{
  echo json_encode($errors);
}


function trigger_email_php($data, $files){
  $to = "sarathlaln88@gmail.com";
  $subject = "You have a new application for admission";

  // Always set content-type when sending HTML email
  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

  // More headers
  // $headers .= 'From: <webmaster@example.com>' . "\r\n";
  // $headers .= 'Cc: myboss@example.com' . "\r\n";

  $message = prepare_html_mail_content($data);

  mail($to,$subject,$message,$headers);

}


function trigger_email_phpmailer($data, $files){
  echo 'mail reached';
  $mail = new PHPMailer();
  $mail->IsSMTP();
  $mail->Mailer = "smtp";

  // $mail->SMTPDebug  = 1;
  // $mail->SMTPAuth   = TRUE;
  // $mail->SMTPSecure = "ssl";
  // $mail->Port       = 465;
  // $mail->Host       = "smtp-relay.sendinblue.com";
  // $mail->Username   = "hello@sarathlal.com";
  // $mail->Password   = "jUg6x3LaKIDrAXkz";

  // $mail->SMTPDebug  = 1;
  // $mail->SMTPAuth   = TRUE;
  // $mail->SMTPSecure = "ssl";
  // $mail->Port       = 465;
  // $mail->Host       = "smtp.gmail.com";
  // $mail->Username   = "admissionholyspirit@gmail.com";
  // $mail->Password   = "Admission@holy1232";

  $mail->SMTPDebug  = 1;
  $mail->SMTPAuth   = TRUE;
  $mail->SMTPSecure = "ssl";
  $mail->Port       = 465;
  $mail->Host       = "mditpoly.ac.in";
  $mail->Username   = "admission@mditpoly.ac.in";
  $mail->Password   = "p37o{L5?psje";

  $mail->IsHTML(true);
  $mail->AddAddress("mathewgeorge89.3021@gmail.com.com", "Mathew");
  $mail->SetFrom("admission@mditpoly.ac.in", "Admission - MDIT");
  #$mail->AddReplyTo("reply-to-email@domain", "reply-to-name");
  #$mail->AddCC("cc-recipient-email@domain", "cc-recipient-name");
  $mail->Subject = "You have a new application for admission";
  $content = prepare_html_mail_content($data);

  if(!empty($files)){
    foreach($files as $file_prop){
      $mail->addAttachment($file_prop['file'], $file_prop['name']);
    }
  }


  $mail->MsgHTML($content);
  if(!$mail->Send()) {
    echo "Some error happens! Try again...";
    //var_dump($mail);
  } else {
    echo "Your application sent successfully";
  }

}



function prepare_html_mail_content($data){
  ob_start();
  ?>
  <!DOCTYPE html>
  <html>
      <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
      <body>
          <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
              <tr>
                  <td align="center" valign="top" style="background-color: #f7f7f7; padding: 70px 0;">
                      <table cellpadding="0" cellspacing="0" border="0" width="600" class="outer_wraper" style="max-width: 600px; width: 600px; margin: auto;">
                          <tbody>
                              <tr>
                                  <td class="inner_wrapper" style="background-color: #ffffff; vertical-align: top; border: 1px solid #dedede; border-radius: 2px;height: 100px; padding: 40px 40px; color: #595959; font-size:14px;">
                                      <!-- CONTENT GOES HERE -->
                                      <h2>You have a new application for admission.</h2>
                                      <table>
                                        <?php foreach($data as $section => $field_data){ ?>
                                          <tr>
                                              <td colspan="2"><h3><?php echo $section; ?></h3></td>
                                          </tr>
                                          <?php foreach($field_data as $label => $value){ ?>
                                            <tr>
                                                <td width="40%"><?php echo $label; ?></td>
                                                <td><?php echo $value; ?></td>
                                            </tr>
                                          <?php } ?>
                                        <?php } ?>
                                      </table>
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                  </td>
              </tr>
          </table>
      </body>
  </html>
<?php
$content = ob_get_contents();
ob_end_flush();
return $content;
}


function byteconvert($input){
    preg_match('/(\d+)(\w+)/', $input, $matches);
    $type = strtolower($matches[2]);
    switch ($type) {
    case "b":
        $output = $matches[1];
        break;
    case "kb":
        $output = $matches[1]*1024;
        break;
    case "mb":
        $output = $matches[1]*1024*1024;
        break;
    return $output;
    }
}
?>
