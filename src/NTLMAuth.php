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
 * Interface for methods of authentication to SQL Server Reporting Services virtual directory
 */
class NTLMAuth implements AuthenticationInterface
{
    /**
     * @var Credential $credential
     *      Credentials information for user authentication.
     */
    private Credential $credential;
    private bool $unrestricted;

    public function __construct(Credential $credential, bool $unrestricted = false)
    {
        $this->credential = $credential;
        $this->unrestricted = $unrestricted;
    }

    /**
     * Sets up options for the cURL to handle NTLM authentication protocol.
     * 
     * @param array $options The array where the cURL handler options are.
     * 
     * @return void
     */
    public function configure(array &$options): void
    {
        $options[CURLOPT_HTTPAUTH] = CURLAUTH_NTLM;
        $options[CURLOPT_UNRESTRICTED_AUTH] = $this->unrestricted;
        $options[CURLOPT_USERPWD] = $this->credential->buildUSERPWD();
    }
}
