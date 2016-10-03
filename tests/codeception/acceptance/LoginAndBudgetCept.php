<?php

// use tests\codeception\_pages\AboutPage;

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure login works');


$I->amOnPage('/user/login');
$I->fillField('#loginform-username', 'joe');
$I->fillField('#loginform-password', '123456');
$I->click('Login');
$I->seeCurrentUrlEquals('/');
$I->see('Immediate Obligations');

