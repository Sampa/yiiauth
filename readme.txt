v.0.3 
	*fixed a bug with updateStatus();
	*the provider and unique user id is now saved in a session upon login so you can access the info easily everywhere
	*so to update a users status:
	$session=new CHttpSession;
	$session->open();
	$provider=$session['provider'];  // get providername
	//$provideruser =$session['provideruser'];  // get the unique user identifier
	$this->updateStatus($provider,"foo"); //update the status to"foo"