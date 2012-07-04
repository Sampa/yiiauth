<?php

class DefaultController extends Controller
{
	public function actionIndex(){
		$this->renderPartial('index');	
	}
	public function actionauthenticatewith( $provider="" ) {

		$hybridauth_config =Yiiauth::hybridAuthConfig();
		
		$error = false;
		$user_profile = false;
		try{
		// create an instance for Hybridauth with the configuration file path as parameter
			$hybridauth = new Hybrid_Auth( $hybridauth_config );

		// try to authenticate the selected $provider
		if ( isset( $_GET['openid'] ) ){
				$provider = "openid";
				$adapter = $hybridauth->authenticate( $provider,array( "openid_identifier" => $_GET['openid'] ) );
			}else{
				$adapter = $hybridauth->authenticate( $provider );

			}
		// grab the user profile
			$user_profile = $adapter->getUserProfile();
			
		}
		catch( Exception $e ){
			// Display the recived error
			switch( $e->getCode() ){ 
				case 0 : $error = "Unspecified error."; break;
				case 1 : $error = "Hybriauth configuration error."; break;
				case 2 : $error = "Provider not properly configured."; break;
				case 3 : $error = "Unknown or disabled provider."; break;
				case 4 : $error = "Missing provider application credentials."; break;
				case 5 : $error = "Authentification failed. The user has canceled the authentication or the provider refused the connection."; break;
				case 6 : $error = "User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again."; 
					     $adapter->logout(); 
					     break;
				case 7 : $error = "User not connected to the provider."; 
					     $adapter->logout(); 
					     break;
			} 

			// well, basically your should not display this to the end user, just give him a hint and move on..
			$error .= "<br /><br /><b>Original error message:</b> " . $e->getMessage(); 
			$error .= "<hr /><pre>Trace:<br />" . $e->getTraceAsString() . "</pre>";  

		}
		/**$user_profile->identifier; //unique id
		$provider; // the provider name
		$_GET['openid'];//the extra_info
		**/
		
		// workOnUser returns an user object
		if ( is_object ($user_profile) ){
		$user = $this->workOnUser($provider,$user_profile->identifier); 
			if ( $this->autoLogin($user) ){
				//successfull login render default/profile.php
				$this->render('profile',
					array(
					'error'=>$error, //string
					'provideruser'=>$user_profile,//object
					'yiiuser'=>$user, //object
					'provider'=>$provider,	//string
					) );
				}else{
					// this is where u go otherwise
					$this->render('authenticatewith',array('error'=>$error,'user_profile'=>$user_profile ) );
					}
			}else{
					echo "Something wrong with ".$provider;
				}
	} 
}?>