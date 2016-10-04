<?php

// use tests\codeception\_pages\AboutPage;

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure login and budgeting works');

$I->amOnPage('/user/login');
$I->fillField('#loginform-username', 'joe');
$I->fillField('#loginform-password', '123456');
$I->click('Login');
$I->seeCurrentUrlEquals('/');
$I->see('Immediate Obligations');

echo "\n";
testValue($I, [ 
        'testName'       => 'Test creating new budgeted value',
        'objectType'     => 'budget',
        'newOrUpdate'    => 'new',
        'categoryId'     => 1033,
        'fillFieldValue' => ['Budgeted Value', 40],
    ]);

testValue($I, [ 
        'testName'       => 'Test updating existing budgeted value',
        'objectType'     => 'budget',
        'newOrUpdate'    => 'update',
        'categoryId'     => 1033,
        'fillFieldValue' => ['Budgeted Value', 70],
    ]);

testValue($I, [ 
        'testName'       => 'Test creating new actual value',
        'objectType'     => 'transaction',
        'newOrUpdate'    => 'new',
        'categoryId'     => 1033,
        'fillFieldValue' => ['Actual Value', 100],
    ]);

testValue($I, [ 
        'testName'       => 'Test updating actual value',
        'objectType'     => 'transaction',
        'newOrUpdate'    => 'update',
        'categoryId'     => 1033,
        'fillFieldValue' => ['Actual Value', 150],
    ]);


function testValue($I, $params)
{
    $valueType = [
        'budget' => 'budgeted',
        'transaction' => 'actual',
    ][$params['objectType']];

    $valueLinkField = "#$valueType-value-category-id-{$params['categoryId']}";

    $formUrlPartial = $params['newOrUpdate'] === 'new' ? 'new?' : "update-$valueType-value-form?id=1&"; 
    $formUrl = "/{$params['objectType']}/{$formUrlPartial}category_id={$params['categoryId']}";

    $I->amGoingTo($params['testName']);
    $fieldValue = $I->grabTextFrom($valueLinkField);
    echo "original Field Value: $fieldValue\n";
    $I->click($valueLinkField);
    $I->seeCurrentUrlEquals($formUrl);
    $I->fillField($params['fillFieldValue'][0], $params['fillFieldValue'][1]);
    $I->click('Save');
    $I->seeCurrentUrlEquals('/cashbook/index');

    if ($params['objectType'] == 'transaction' && $params['newOrUpdate'] == 'update') {
        // we're adding form value to original field value
        $fieldValue = $fieldValue + $params['fillFieldValue'][1];
    } else {
        // we're replacing original value with the form value
        $fieldValue = $params['fillFieldValue'][1];
    }
    echo "new Field Value: $fieldValue\n";
    $I->see($fieldValue, $valueLinkField);
}

