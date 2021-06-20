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
 * A published .rdl report to be called on SQL Server Reporting Services
 */
class Report
{
    /**
     * @var string $path
     *      Path to the published .rdl file without the extension
     */
    private string $path;

    /**
     * @var array $parameters
     *      An associative array with parameters
     */
    private array $parameters = [];

    /**
     * Creates a new instance of Report
     * 
     * @param string $path Path to the published .rdl file without the extension
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getParams(): array
    {
        return $this->parameters;
    }    
    public function hasParams():bool
    {
        return count($this->parameters)>0;
    }
    public function getParam(string $name)
    {
        return $this->parameters[$name];
    }

    /**
     * Add a new parameter to the collection of parameters or overwrite the previous value if the parameter already exists in the collection.
     * 
     * @param string $name  The name of the parameter
     * @param string $value The value of the parameter
     * 
     * @return void
     */
    public function addParam(string $name, string $value): void
    {
        $this->parameters[$name] = $value;
    }
}
