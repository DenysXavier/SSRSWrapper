<?php

/**
 * Copyright 2021 Denys Xavier
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace DenysXavier\SSRSWrapper;

/**
 * Class that holds information like username and password that is intented to be used by any authentication class.
 */
class Credential
{
    /**
     * @var string $username
     *      Username used for authentication.
     */
    private string $username;

    /**
     * @var string $password
     *      Plain text password used for authentication.
     */
    private string $password;

    /**
     * @var string $domain
     *      Domain or realm in wich the user belongs.
     */
    private string $domain;

    /**
     * Creates a new instance of Credential.
     * 
     * @param string $username Username used for authentication.
     * @param string $password Plain text password used for authentication.
     * @param string $domain   Domain or realm in wich the user belongs.
     */
    public function __construct(string $username, string $password, string $domain = '')
    {
        $this->username = $username;
        $this->password = $password;
        $this->domain = $domain;
    }

    /**
     * Builds a formatted string to be used as credentials for the cURL connection.
     * 
     * @return string
     */
    public function buildUSERPWD(): string
    {
        $userpwd = "";

        if (!empty($this->domain)) {
            $userpwd = $this->domain . "/";
        }

        $userpwd .= $this->username . ":" . $this->password;

        return $userpwd;
    }
}
