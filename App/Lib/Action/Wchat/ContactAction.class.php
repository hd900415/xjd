<?php

class ContactAction extends CommonAction
{
    /**
     * 上传联系人
     * @return void
     */
    public function ajaxPush()
    {
        if ($this->isPost()) {
            $userContactModel = D("UserContacts");

            $userInfo = $this->isLogin();
            if (empty($userInfo)) {
                $this->setAjaxResponse(400, "", "User is not logged in");
            }
            $contacts=$_POST["contacts"];
            if (empty($contacts)) {
                $this->setAjaxResponse(300, "", "Contacts Parameter is required");
            }
            if ($userInfo) {
                $addressBook = [];

                foreach ($contacts as $contact) {

                    if($contact["name"]&&$contact["phone"]){
                        $addressBook[] = [
                            "uid" => $userInfo['id'],
                            "uname" => $userInfo['telnum'],
                            "contact_name" => $contact["name"],
                            "contact_phone" => $contact["phone"],
                            "contact_relate" =>isset($contact["relate"])?$contact["relate"]:"",
                            "sort" => 0,
                            "status" => 0,
                            "created_at" => time()
                        ];
                    }
                }
                $userContactModel->addAll($addressBook);
                $this->ajaxReturn(array(
                    "status" => 200,
                    "message" => 'Successful',
                    "data" => ""
                ));

            } else {
                $this->setAjaxResponse(403, "", 'The user is not logged in.Please login first');
            }
        } else {
            $this->setAjaxResponse(405, "", 'Unsupported request Method');
        }
    }
}