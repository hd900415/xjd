<?php

class FeedbackAction extends CommonAction
{

    public function index()
    {

    }

    public function ajaxCreate()
    {
        if ($this->isPost()) {
            $comments = I("comments");
            $attachments = I("attachments");
            $contact = I("contact");
            $userInfo = $this->isLogin();
            if ($userInfo) {
                if (!$comments) {
                    $this->setAjaxResponse(300, '', "comments is required");
                }
                if (!$contact) {
                    $this->setAjaxResponse(300, '', "contact is required");
                }
                $feedbackModel = D("Feedback");
                $feedInfo = array(
                    "uid" => $userInfo["id"],
                    "uname" => $userInfo['telnum'],
                    "content" => $comments,
                    "attachment" => $attachments,
                    "contact" => $contact,
                    "add_time" => time(),
                );
                if ($feedbackModel->add($feedInfo)) {
                    $this->setAjaxResponse(200, '', "Submit Successfully");
                } else {
                    $this->setAjaxResponse(500, '', "Submit Failed");
                }

            } else {
                $this->setAjaxResponse(403, '', "The user is not logged in.Please login first");
            }

        }
        $this->setAjaxResponse(405, 'Unsupported request Method');
    }

}