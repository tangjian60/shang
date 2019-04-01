<?php

	class Cookiefactory {
		function __construct(){
			//TODO:
		}

		public function setRecommendId( $user_id ) {

			if ( empty($user_id) || !is_numeric($user_id) ) {
				return ;
			}

			setcookie(COOKIE_RECOMMEND_ID, $user_id, time()+3600*24, '/');
		}

		public function getRecommendId(){

			if( isset($_COOKIE[COOKIE_RECOMMEND_ID]) ) {
				if ( !empty($_COOKIE[COOKIE_RECOMMEND_ID]) && is_numeric($_COOKIE[COOKIE_RECOMMEND_ID]) ) {
					return $_COOKIE[COOKIE_RECOMMEND_ID];
				}
			}

			return ;
		}
	}
?>