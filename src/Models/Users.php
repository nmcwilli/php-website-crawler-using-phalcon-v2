<?php
declare(strict_types=1);

/**
 * Users Model - (Main Crawler Model)
 * This is the Users model, which works to interface with users as part of the crawler. 
 * By Neil McWilliam
 */

namespace Invo\Models;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;

class Users extends Model
{
    public function validation()
    {
        $validator = new Validation();
        
        $validator->add(
            'email',
            new EmailValidator(
                [
                    'message' => 'Invalid email given',
                ]
            )
        );
        $validator->add(
            'email',
            new UniquenessValidator(
                [
                    'message' => 'Sorry, The email was registered by another user',
                ]
            )
        );
        $validator->add(
            'username',
            new UniquenessValidator(
                [
                    'message' => 'Sorry, That username is already taken',
                ]
            )
        );
        
        return $this->validate($validator);
    }
}
