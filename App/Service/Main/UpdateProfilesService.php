<?php
    require_once('../../Controller/Main/UpdateProfilesController.php');
    require_once('../../Config/db_connection.php');

    use App\Controller\Main\UpdateProfilesController;

    /**
     * `functionHandler` Handler the controller and its method.
     * @return Mixed
     */
    function functionHandler() {
        date_default_timezone_set('Asia/Manila');
        
        $output = [];

        $controller = new UpdateProfilesController(connectToDb());

        $methodName = str_replace("/", '', $_SERVER['PATH_INFO']);

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $output = (empty($_GET)) ? $controller->$methodName() : $controller->$methodName($_GET);
                echo json_encode($output);
                break;

                case 'POST':
                // $postData = file_get_contents("php://input");
                // $request  = json_decode($postData);

                // $output = $controller->$methodName($request);
                // echo json_encode($output);
                
                $output = $controller->$methodName($_POST, $_FILES);
                echo json_encode($output);
                break;

            case 'DELETE':
                $postData = file_get_contents("php://input");
                $request  = json_decode($postData);

                $output = $controller->$methodName($request->id);
                echo json_encode($output);
                break;

            default:
                break;
        }
    }

    /**
     * Run `functionHandler` function
     */
    functionHandler();