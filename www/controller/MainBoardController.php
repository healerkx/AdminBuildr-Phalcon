<?php


class MainBoardController extends AbBaseController
{

    const CurrentAdmin = 'currentAdmin';

    public function indexAction() {

        $current = $this->session->get(self::CurrentAdmin);

        $data = json_decode($current, true);
        if ($data)
        {
            echo "Index";
        }
    }

    public function lockAction()
    {
        $loginAction = 'mainBoard/login';
        $data = [
            'loginAction' => $loginAction
        ];
        parent::showPage('common/login', $data);
    }

    /**
     * Admin Login handler
     */
    public function loginAction()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $target = 'mainBoard/lock';
        if ($this->login($username, $password))
        {
            $target = 'mainBoard/index';
        }

        $this->response->redirect($target);
    }

    public function logoutAction()
    {
        $this->session->remove(self::CurrentAdmin);
        $target = 'mainBoard/lock';
        $this->response->redirect($target);
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    private function login($username, $password)
    {
        $passwordHash = '';
        $condition = "username='$username' AND password='{$passwordHash}'";
        $admin = KxAdminUser::findFirst($condition);

        if ($admin)
        {
            $adminData = $admin->toArray();
            $this->session->set(self::CurrentAdmin, json_encode($adminData));

            // $out = $this->session->get(self::CurrentAdmin);
        }
        return true;
    }

}