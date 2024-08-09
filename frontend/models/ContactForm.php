<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $email;
    public $name;
    public $body;
    public $phone;
    public $verifyCode;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['verifyCode', 'captcha'],
            // name, email, phone and body are required
            [['email', 'body'], 'required'],
            [['email', 'name', 'body', 'phone'], 'string', 'max' => 255],
            // email has to be a valid email address
            ['email', 'email'],
            ['phone', 'match', 'pattern' => '/\+?\d{10,15}/', 'skipOnEmpty' => true, 'skipOnError' => false],
            // verifyCode needs to be entered correctly
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param string $email the target email address
     * @return bool whether the email was sent
     */
    public function sendEmail($email)
    {
        return Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setReplyTo([$this->email => $this->email])
            ->setSubject('Feedback from ' . $this->email)
            ->setTextBody($this->body)
            ->send();
    }
}
