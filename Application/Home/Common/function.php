<?php
/**
 * sendMail ͨ��PHPMailer�����ʼ�
 * @param  string $to        �ռ���
 * @param  string $subject   �ʼ�����
 * @param  string $content   �ʼ�����
 * @return false             ���ͽ�� 
 */
function sendMail($to, $subject, $content)
{
  Vendor('PHPMailer.PHPMailerAutoload');

  $mailer = new PHPMailer();

  $mailer->isSMTP();
  $mailer->Host = C('MAIL_HOST');
  $mailer->SMTPAuth = C('MAIL_AUTH');
  $mailer->Username = C('MAIL_USERNAME');
  $mailer->Password = C('MAIL_PASSWORD');
  $mailer->CharSet  = C('MAIL_CHARSET');
  $mailer->Port = C('MAIL_PORT');

  $mailer->From = C('MAIL_USERNAME');
  $mailer->FromName = C('MAIL_FROMNAME');
  $mailer->addAddress($to);
  $mailer->isHTML(C('MAIL_HTML'));
  $mailer->Subject = $subject;
  $mailer->Body = $content;

  if($mailer->send()) {
      return true; 
  }else{
      return false;
  }
}

/**
 * get_ajax_res ��Ϸ�����Ϣ
 * @param  intger $status    ״̬�룺0->ʧ�ܣ�1->�ɹ�
 * @param  string $message   ������Ϣ
 * @return array             ��Ϻ����� 
 */
function get_ajax_res($status,$message)
{
  $data['status']  = $status;
  $data['message'] = $message;
  return $data;
}

function setCounts($prefix,$worksID,$field,$opera='+',$expire=1800)
{ 

  // echo $prefix,$worksID,$field,$opera;
  $worksFlag  = $prefix.'-'.$worksID;

  $countsDB = M('works_counts');
  $now      = NOW_TIME;
  // dump(F($worksFlag));exit;
  if (!F($worksFlag)) {
      $countsInfo = $countsDB->where('works_id = '.$worksID)->find();
      $clickInit = array(
          'collect_counts' =>  $countsInfo['collect_counts'],
          'comment_counts' =>  $countsInfo['comment_counts'],
          'praise_counts'  =>  $countsInfo['praise_counts'],
          'click_counts'   =>  $countsInfo['click_counts'],
          'expire'         =>  $now+$expire,
      );
      F($worksFlag,$clickInit);
      // dump(F($worksFlag));
  }

  $clickData = F($worksFlag);

  if ($opera == '+') {
    $clickData[$field]+=1;
  }else{
    $clickData[$field]-=1;
  }

  if ($clickData['expire'] < $now) {
    $countsDB->where('works_id = '.$worksID)->setField($field,$clickData[$field]);
    $clickData['expire'] = $now + $expire;
  }
  
  F($worksFlag,$clickData);

 // dump(F($worksFlag));


  return F($worksFlag);
}

?>