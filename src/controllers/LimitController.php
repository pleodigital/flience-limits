<?php
/**
 * Users Limit plugin for Craft CMS 3.x
 *
 * Limit user to download 20 wallpapers per month
 *
 * @link      flience.com
 * @copyright Copyright (c) 2019 Trebuh
 */

namespace flience\userslimit\controllers;

use flience\userslimit\UsersLimit;

use Craft;
use craft\web\Controller;
use craft\db\Query;
use craft\db\ActiveRecord;
use craft\db\Connection;

/**
 * Default Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Trebuh
 * @package   UsersLimit
 * @since     1.0.0
 */
class LimitController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something', 'get-limit', 'is-logged-in', 'max-limit', 'get-account-type', 'upgrade-account'];


    // Public Methods
    // =========================================================================

    public function actionAddDownload() {
        $db = new Connection([
            "dsn" => getenv('DB_DRIVER').":host=".getenv('DB_SERVER').";dbname=".getenv('DB_DATABASE'),
            "username" => getenv('DB_USER'),
            "password" => getenv('DB_PASSWORD'),
            "charset" => "utf8"
        ]);

        for ($i = 0; $i < $_POST["amountOfWallpapers"]; $i++) {
            $db->createCommand()->insert("userslimit", [
                "downloaded" => Craft::$app->getUser()->getId(),
                "dateCreated" => date("Y-m-d H:i:s")
            ])->execute();
        }

        $query = new Query;

        // $select = count($db->createCommand("SELECT * FROM userslimit WHERE id='".Craft::$app->getUser()->getId()."'"));
        $select = $query->select("*")->from("userslimit")->where("downloaded=".Craft::$app->getUser()->getId())->andWhere("dateCreated>CURDATE() - INTERVAL 1 MONTH")->count();

        $limit = UsersLimit::getInstance()->settings->limit;

        if ($select > $limit) {
            return 0;
        } else {
            return $select;
        }

        // return Craft::$app->getUser()->id;
        // return CJSON::encode($select);
        // return $select;
        // return 1;
    }

    public function actionGetLimit() {
        if (Craft::$app->getUser()->getId()) {

            $query = new Query();

            $select = $query->select("*")->from("userslimit")->where("downloaded=".Craft::$app->getUser()->getId())->andWhere("dateCreated>CURDATE() - INTERVAL 1 MONTH")->count();

            $limit = UsersLimit::getInstance()->settings->limit;

            if ($select <= $limit) {
                return $limit - $select;
            } else {
                return $select;
            }
        } else {
            return 'error';
        }
    }

    public function actionIsLoggedIn() {
        if ((Craft::$app->getUser()->getId()) !== null) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/users-limit/default
     *
     * @return mixed
     */
    public function actionMaxLimit() {

        if (Craft::$app->getUser()->getId()) {
            return UsersLimit::getInstance()->settings->limit;
        } else {
            return 'error';
        }
    }

    public function actionGetAccountType() {
        $user = Craft::$app->getUser();
        return $user->getIdentity()->getFieldValue('isPremium') ? 1 : 0;
    }

    public function actionUpgradeAccount() {
//        print_r(Craft::$app->getUser());
//        $update = Craft::$app->getUser()->getBehaviors()->;
//        Craft::$app->elements->saveElement($update);
//        return 'UP';
    }

    /**
     * Handle a request going to our plugin's actionDoSomething URL,
     * e.g.: actions/users-limit/default/do-something
     *
     * @return mixed
     */
    public function actionDoSomething()
    {
        return UsersLimit::getInstance()->getSettings()->limit;
    }
}
