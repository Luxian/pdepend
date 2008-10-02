<?php
/**
 * This file is part of PHP_Reflection.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Ast
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/Ast/AbstractType.php';
require_once 'PHP/Reflection/Ast/Iterator/TypeFilter.php';

/**
 * Representation of a code interface.  
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Ast
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Ast_Interface extends PHP_Reflection_Ast_AbstractType
{
    /**
     * Returns <b>true</b> if this is an abstract class or an interface.
     *
     * @return boolean
     */
    public function isAbstract()
    {
        return true;
    }
    
    /**
     * Returns an iterator with all implementing classes.
     *
     * @return PHP_Reflection_Ast_Iterator
     * @todo TODO: Should we return all implementing classes? This would include
     *             all classes that extend a implementing classes and all classes
     *             that implement a child interface.
     */
    public function getImplementingClasses()
    {
        $type = 'PHP_Reflection_Ast_Class';
        
        $children = new PHP_Reflection_Ast_Iterator($this->children);
        $children->addFilter(new PHP_Reflection_Ast_Iterator_TypeFilter($type));
        
        return $children;
    }
    
    /**
     * Returns an iterator with all parent, parent parent etc. interfaces.
     *
     * @return PHP_Reflection_Ast_Iterator
     */
    public function getParentInterfaces()
    {
        $interfaces = array();
        foreach ($this->getDependencies() as $interface) {
            // Append parent interface first 
            if (in_array($interface, $interfaces, true) === false) {
                $interfaces[] = $interface;
            }
            // Append parent parents
            foreach ($interface->getParentInterfaces() as $parentInterface) {
                if (in_array($parentInterface, $interfaces, true) === false) {
                    $interfaces[] = $parentInterface;
                }
            }
        }
        return new PHP_Reflection_Ast_Iterator($interfaces);
    }
    
    /**
     * Returns an iterator with all child interfaces.
     *
     * @return PHP_Reflection_Ast_Iterator
     */
    public function getChildInterfaces()
    {
        $type = 'PHP_Reflection_Ast_Interface';
        
        $children = new PHP_Reflection_Ast_Iterator($this->children);
        $children->addFilter(new PHP_Reflection_Ast_Iterator_TypeFilter($type));
        
        return $children;
    }
    
    /**
     * Checks that this user type is a subtype of the given <b>$type</b> instance.
     *
     * @param PHP_Reflection_Ast_AbstractType $type The possible parent type 
     *                                              instance.
     * 
     * @return boolean
     */
    public function isSubtypeOf(PHP_Reflection_Ast_AbstractType $type)
    {
        if ($type === $this) {
            return true;
        } else if ($type instanceof PHP_Reflection_Ast_Interface) {
            foreach ($this->getParentInterfaces() as $interface) {
                if ($interface === $type) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Visitor method for node tree traversal.
     *
     * @param PHP_Reflection_VisitorI $visitor The context visitor implementation.
     * 
     * @return void
     */
    public function accept(PHP_Reflection_VisitorI $visitor)
    {
        $visitor->visitInterface($this);
    }
    
}