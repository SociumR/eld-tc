<?php


use Phalcon\Cli\Task;

class AdminTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }

    /**
     * @param array $params
     */
    public function testAction(array $params)
    {
        echo sprintf('hello %s', $params[0]);

        echo PHP_EOL;

        echo sprintf('best regards, %s', $params[1]);

        echo PHP_EOL;
    }


    /**
     * @param array $params
     * @throws Exception
     */
    public function createUserAction(array $params)
    {
        if (sizeof($params) !== 5) {
            throw new Exception('may be only 5 parameters');
        }
        $class = new \stdClass();
        $class->email = $params[0];
        $class->password = $params[1];
        $class->username = $params[2];
        $class->role = $params[3];

        $server = $params[4];

        $data = $class;
        $data_string = json_encode($data);

        $ch = curl_init($server);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        if($info['http_code'] == 200) {
            echo 'User created' . PHP_EOL;
        } else {
            throw new Exception($result);
        }

        /*$email = $params[0];
        $password = $params[1];
        $username= $params[2];
        $role = $params[3];*/

        //$user = new \App\Services\AuthService();

        //print_r($user);
        die();
    }

    public function initRole(array $params)
    {
        if (sizeof($params) !== 5) {
            throw new Exception('may be only 5 parameters');
        }
        $class = new \stdClass();
        $class->email = $params[0];
        $class->password = $params[1];
        $class->username = $params[2];
        $class->role = $params[3];

        $server = $params[4];

        $data = $class;
        $data_string = json_encode($data);

        $ch = curl_init($server);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        if($info['http_code'] == 200) {
            echo 'User created' . PHP_EOL;
        } else {
            throw new Exception($result);
        }

        /*$email = $params[0];
        $password = $params[1];
        $username= $params[2];
        $role = $params[3];*/

        //$user = new \App\Services\AuthService();

        //print_r($user);
        die();
    }

    private function curl($method, $url, $data)
    {

    }
}