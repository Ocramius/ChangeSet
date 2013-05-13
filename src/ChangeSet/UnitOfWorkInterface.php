<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ChangeSet;

/**
 * Simple UnitOfWork as described in PoEAA
 *
 * @link    http://martinfowler.com/eaaCatalog/unitOfWork.html
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
interface UnitOfWorkInterface
{
    /**
     * Adds the provided object to the unit of work and tracks it as scheduled insert
     *
     * @param object $object
     *
     * @return void
     */
    public function registerNew($object);

    /**
     * Registers the provided object as scheduled for dirty check and update
     *
     * @param object $object
     *
     * @return void
     */
    public function registerDirty($object);

    /**
     * Registers the provided object as clean
     *
     * @param object $object
     *
     * @return void
     */
    public function registerClean($object);

    /**
     * Registers the provided object as scheduled for deletion
     *
     * @param object $object
     *
     * @return void
     */
    public function registerDeleted($object);

    /**
     * Commits all the scheduled changes
     *
     * @return void
     */
    public function commit();

    /**
     * Rolls back all the registered objects to the state at which they were
     * originally registered
     *
     * @return void
     */
    public function rollback();
}
