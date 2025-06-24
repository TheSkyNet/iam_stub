<?php

namespace IamLab\Service;


use IamLab\Core\API\aAPI;

/**
 * @property void isAuthenticated
 */
class Admin extends aAPI
{


  function indexAction()
  {
  //  $this->isAuthenticated;

    echo $this->view->render('admin');

  }


}