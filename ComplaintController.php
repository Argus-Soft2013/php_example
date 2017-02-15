<?php
namespace backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;

use common\models\User;
use common\models\UserInfo;
use common\models\UserInfoCompiled;
use common\models\Search;
use common\models\LoginForm;
use common\models\Level;
use common\models\Review;
use common\models\SystemAlert;
use common\models\UserComplaint;
use common\models\UserParameter;
use common\models\UserParameterValue;

use backend\controllers\BackendController;

/**
 * Site controller
 */
class ComplaintController extends BackendController {
		
    const COMPLAINT_SELECT_LIMIT = 10;
	
	public function actionGetAll() {

		$page = $this->getPost('page');
		if (!$page) {
			
			$page = 0;
		}
		$provider = new ActiveDataProvider([
			'query'			=> UserComplaint::find()
				->joinWith('reason')
				->joinWith('complainer AS complainer')
				->joinWith('profile')
				->joinWith('review')
				->where(['status' => [
					UserComplaint::COMPLAINT_STATUS_WAITING,
					UserComplaint::COMPLAINT_STATUS_PROCESSING
				]])
				->asArray(),
			'sort'			=> ['defaultOrder' => ['date' => SORT_DESC]],
			'pagination'	=> [
				'pageSize'	=> self::COMPLAINT_SELECT_LIMIT,
				'page'		=> $page
			],
		]);
		
		return $this->getOkResponse([
			'total'		=> $provider->getTotalCount(),
			'page'		=> $page,
			'perPage'	=> self::COMPLAINT_SELECT_LIMIT,
			'data'		=> $provider->getModels()
		]);
	}
	
	public function actionSetStatus() {
		
		$complaintId = $this->getPost('id');
		
		if (!$complaint = UserComplaint::findOne($complaintId)) {
			
			return $this->getErrorResponse(self::ERROR_WRONG_OR_EMPTY_DATA);
		}
		
		$status = $this->getPost('status');
		if (!in_array($status, UserComplaint::getStatuses())) {
			
			return $this->getErrorResponse(self::ERROR_WRONG_OR_EMPTY_DATA);
		}
		
		$complaint->status = $status;
		return $this->getOkResponse($complaint->save());
	}
	
	public function actionGetStatusesList() {
		
		return $this->getOkResponse(UserComplaint::getStatuses());
	}
	
	// protected methods
}