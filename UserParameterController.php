<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\UserParameter;
use common\models\UserParameterValue;
use common\models\UserParameterGroup;

/**
 * Site controller
 */
class UserParameterController extends BackendController {
    
    const ERROR_WRONG_OR_EMPTY_ID = 'Wrong or empty user parameter id.';
    
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

        $userId = $this->getPost('user_id');
        if (!$userId) {
            
            return $this->getErrorResponse(self::ERROR_WRONG_OR_EMPTY_ID);
        }
        $result = [];
        $data = UserParameterValue::find()->joinWith('group')->where(['user_id' => $userId])->all();
        foreach ($data as $parameter) {

            $result[] = $this->getFormattedParameter($parameter);
        }
        return $this->getOkResponse(
            $result
        );
    }
    
    public function actionGet() {
        
        $userId = $this->getPost('user_id');
        $parameterId = $this->getPost('id');
        if (!$parameterId) {
            
            return $this->getErrorResponse(self::ERROR_WRONG_OR_EMPTY_ID);
        }
        return $this->getOkResponse(
            $this->getFormattedParameter(
                UserParameterValue::find()
                    ->where(['parameter_id' => $parameterId, 'user_id' => $userId])
                    ->joinWith('group')
                    ->one()
            )
        );
    }
    
    public function actionAdd() {
        
        $id = $this->getPost('id');
        $userId = $this->getPost('user_id');
        if (!$userId || !$id || !($userParameter = UserParameter::findOne($id))) {
            
            return $this->getErrorResponse(self::ERROR_WRONG_OR_EMPTY_ID);
        }
        $userParameterValue = UserParameterValue::findOne(['user_id' => $userId, 'parameter_id' => $id]);
        $userParameterValue->value = $this->getPost('value');
        if ($userParameterValue->save()) {

            return $this->getOkResponse();
        }
        return $this->getErrorResponse($userParameter->getFirstErrors());
    }
    
    public function actionEdit() {
        
        $id = $this->getPost('id');
        $userId = $this->getPost('user_id');
        if (!$userId || !$id || !($userParameter = UserParameter::findOne($id))) {
            
            return $this->getErrorResponse(self::ERROR_WRONG_OR_EMPTY_ID);
        }

        $userParameterValue = UserParameterValue::findOne(['user_id' => $userId, 'parameter_id' => $id]);
        $userParameterValue->value = $this->getPost('value');
        if ($userParameterValue->save()) {

            return $this->getOkResponse();
        }
        return $this->getErrorResponse($userParameter->getFirstErrors());
    }

}