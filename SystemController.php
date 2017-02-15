<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\SystemParameter;
use common\models\SystemParameterGroup;

/**
 * Site controller
 */
class SystemController extends BackendController {
    
    const ERROR_WRONG_OR_EMPTY_ID = 'Wrong or empty system parameter id.';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionGetAll() {

        return $this->getOkResponse(SystemParameter::find()->joinWith('group')->asArray()->all());
    }
    
    public function actionGet() {
        
        $parameterId = Yii::$app->request->post('id');
        if (!$parameterId) {
            
            return $this->getErrorResponse(self::ERROR_WRONG_OR_EMPTY_ID);
        }
        return $this->getOkResponse(SystemParameter::findOne($parameterId));
    }
    
    public function actionAdd() {
        
        $systemParameter = new SystemParameter();
        $systemParameter->name = Yii::$app->request->post('name');
        $systemParameter->value = Yii::$app->request->post('value');
        $systemParameter->description = Yii::$app->request->post('description');
        if ($systemParameter->save()) {

            return $this->getOkResponse(['id' => $systemParameter->id]);
        }
        return $this->getErrorResponse($systemParameter->getFirstErrors());
    }
    
    public function actionEdit() {
        
        $id = (int) Yii::$app->request->post('id');
        if (!$id || !($systemParameter = SystemParameter::findOne($id))) {
            
            return $this->getErrorResponse(self::ERROR_WRONG_OR_EMPTY_ID);
        }

        $systemParameter->value = Yii::$app->request->post('value');
        $systemParameter->description = Yii::$app->request->post('description');
        if ($systemParameter->save()) {

            return $this->getOkResponse();
        }
        return $this->getErrorResponse($systemParameter->getFirstErrors());
    }
    
    /** groups **/
    
    public function actionGroupgetAll() {
        
        return $this->getOkResponse(SystemParameterGroup::find()->asArray()->all());
    }
}