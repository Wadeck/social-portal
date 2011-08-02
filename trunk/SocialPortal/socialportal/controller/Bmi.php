<?php

namespace socialportal\controller;
use socialportal\model\ChartBmi;

use socialportal\common\form\custom\AddBmiValueForm;

use core\http\exceptions\AccessDeniedException;

use OFC\Charts\Line\OFC_Charts_Line_Hollow;

use OFC\Charts\OFC_Charts_Tooltip;

use OFC\Elements\Axis\OFC_Elements_Axis_X_Label_Custom;

use OFC\Elements\Axis\OFC_Elements_Axis_X_Label_Set;

use OFC\Elements\Axis\OFC_Elements_Axis_X_Label;

use OFC\Charts\OFC_Charts_Scatter_Value;

use OFC\Charts\OFC_Charts_Line;

use OFC\Elements\Axis\OFC_Elements_Axis_X;

use OFC\OFC_Chart;

use OFC\Elements\Axis\OFC_Elements_Axis_Y;

use OFC\Charts\Line\OFC_Charts_Line_Dot;

use OFC\Elements\OFC_Elements_Title;

use core\ClassLoader;

use socialportal\common\templates\ProfileTabTemplate;

use socialportal\common\templates\ProfileInformationTemplate;

use socialportal\common\templates\ProfileToolTemplate;

use socialportal\common\form\custom\ProfilePrivacyForm;

use core\tools\ImageUtils;

use socialportal\model\Token;

use core\tools\Mail;

use core\security\Crypto;

use socialportal\common\form\custom\ProfileEditEmailForm;

use socialportal\common\form\custom\ProfileEditPasswordForm;

use socialportal\common\form\custom\ProfileEditUsernameForm;

use core\debug\Logger;

use core\Config;

use socialportal\model\User;

use core\tools\Utils;

use socialportal\common\form\custom\ProfileForm;

use socialportal\model\UserProfile;

use socialportal\common\form\custom\RegisterForm;

use core\user\UserHelper;

use socialportal\common\form\custom\LoginForm;

use core\FrontController;

use core;

use core\AbstractController;
use core\user\UserRoles;
use DateTime;

class Bmi extends AbstractController {
	
	/**
	 * @Nonce(getValues)
	 * @GetAttributes(userId)
	 */
	public function getValuesAction() {
		$get = $this->frontController->getRequest ()->query;
		$userId = $get->get ( 'userId' );
		
		$linkWithPlaceHolder = $this->frontController->getViewHelper()->createHrefWithNonce('removeValueBmi', 'Bmi', 'removeValueBmi', 
			array('userId' => $userId, 'itemId' => '%item_id%' ) );
		$linkWithPlaceHolder = Utils::getBaseUrlWithoutName() . $linkWithPlaceHolder;
			
		$bmiRepo = $this->em->getRepository ( 'ChartBmi' );
		$bmiItems = $bmiRepo->findBmiInfo ( $userId);
		
		if (false === $bmiItems) {
			return;
		}
		
		$dates = array ();
		$values = array ();
		$minDate = 2147483647;
		$maxDate = 0;
		
		$minBmi = 2147483647;
		$maxBmi = 0;
		
		foreach ( $bmiItems as $item ) {
			$date = $item->getDate();
			$timestamp = $date->getTimestamp();
			if($timestamp < $minDate){
				$minDate = $timestamp;
			}
			if($maxDate < $timestamp){
				$maxDate = $timestamp;
			}
			$bmi = $item->getItem();
			$bmi /= 10;
			if($bmi < $minBmi){
				$minBmi = $bmi;
			}
			if($maxBmi < $bmi){
				$maxBmi = $bmi;
			}
			$dates [] = $date->format('j M Y');
			$scatter = new OFC_Charts_Scatter_Value( $timestamp, $bmi);
			$scatter->{'on-click'} = strtr( $linkWithPlaceHolder, array('%item_id%' => $item->getId() ) );
//			$scatter->{'index'} = $item->getId();
			$values [] = $scatter;
//			$values [] = $item->getItem();
		}
		
		$title = new OFC_Elements_Title( __( 'Bmi evolution' ) );
		$line = new OFC_Charts_Line();
		$line->set_values ( $values );
		$line->set_tooltip("BMI: #val#\n#date:j M Y#\nClick to remove");
		$line->set_dot_style('hollow-dot');
		$line->set_dot_size( 4 );
		$line->set_halo_size ( 2 );
		$line->set_on_click('bmi_click');
		
		$minDate = mktime(0, 0, 0, date("m", $minDate), 0, date("Y", $minDate));
		$maxDate = mktime(0, 0, 0, date("m", $maxDate)+1, 0, date("Y", $maxDate));
		
		$diffDate = $maxDate - $minDate;
		$numMonthDiff = date("n", $diffDate) + 12*(date("Y", $diffDate) - 1970);
		// dunno why, but works, the internal as3 must be bugged
		$numMonthDiff = max(2, $numMonthDiff);
		$coefMonth = (integer)($numMonthDiff / 5);
		$coefMonth = max(1, $coefMonth);
		
		$current = 0;
		$flag = false;
		do{
			if($current >= $numMonthDiff){
				$flag = true;
			}
			$currentMonth = mktime(0, 0, 0, date("m", $minDate)+$current, 1, date("Y", $minDate));
			$datesLabel[] = array('x' => $currentMonth);
			$current += $coefMonth;
		}while($current < $numMonthDiff || !$flag);
		
		// previous point where we trunc the dates 
		
		$labelX = new OFC_Elements_Axis_X_Label_Custom('#date:j M Y#', '#ddd000', 12, 90);
		$labelX->set_labels($datesLabel);
		$labelX->set_visible();
		$labelX->set_colour('#000000');
		
		$x = new OFC_Elements_Axis_X();
		$x->set_labels ($labelX);
		$x->set_range( $minDate, $maxDate+172800 );
		$x->set_stroke(1);
		
		$y = new OFC_Elements_Axis_Y();
		$minBmi = ((integer)($minBmi / 5) -1 )*5;
		$minBmi = max(0, min($minBmi, 65));
		$maxBmi = ((integer)(($maxBmi) / 5) +1 )*5;
		$maxBmi = max(5, min($maxBmi, 70));
		$y->set_stroke(1);
		$y->set_range ( $minBmi, $maxBmi, 5 );

		$chart = new OFC_Chart();
		$chart->set_title ( $title );
		$chart->add_element ( $line );
		$chart->set_x_axis ( $x );
		$chart->set_y_axis ( $y );
		$chart->set_bg_colour('#ffffff');
		echo $chart->toPrettyString();
	}
	
	/**
	 * @Nonce(removeValueBmi)
	 * @GetAttributes({userId, itemId})
	 */
	public function removeValueBmiAction(){
		$get = $this->frontController->getRequest ()->query;
		$userId = $get->get ( 'userId' );
		$itemId = $get->get('itemId');
		
		$bmiRepo = $this->em->getRepository( 'ChartBmi' );
		$result = $bmiRepo->removeItem( $userId, $itemId );
		if(false === $result){
			$this->frontController->addMessage(__('The deletion was a failure'), 'error');
		}else{
			$this->frontController->addMessage(__('The deletion was a success'), 'correct');
		}
		$this->frontController->doRedirectWithNonce('displayProfile', 'Profile', 'display', array('userId' => $userId, 'tab' => 'bmi') );
	}
	
	/**
	 * @Method(POST)
	 * @Nonce(addValue)
	 * @GetAttributes(userId)
	 */
	public function addValueAction() {
		$get = $this->frontController->getRequest ()->query;
		$userId = $get->get ( 'userId' );
		
		// verify the current user is self
		if($userId <= 2 || $userId !== $this->frontController->getCurrentUser()->getId()){
			$this->frontController->generateException(new AccessDeniedException('Bmi', 'addValue'));
		}
		
		$form = new AddBmiValueForm($this->frontController);
		$form->setupWithArray();
		$form->checkAndPrepareContent();
		
		$bmi = $form->getBmiValue();
		$date = $form->getDate();
		
		$bmiChart = new ChartBmi();
		$bmiChart->setUserId($userId);
		$datetime = new DateTime("@$date");
		$bmiChart->setDate($datetime);
		$bmiChart->setItem($bmi);
		
		$this->em->persist($bmiChart);
		if( !$this->em->flushSafe() ){
			$this->frontController->addMessage(__('Error during insertion of the bmi value'), 'error');
		}else{
			$this->frontController->addMessage(__('Insertion completed'), 'correct');
		}
		$this->frontController->doRedirectWithNonce('displayProfile', 'Profile', 'display', array('userId' => $userId, 'tab' => 'bmi') );
	}

}

