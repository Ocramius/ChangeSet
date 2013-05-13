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

namespace ChangeSet\ChangeWriter;

use ChangeSet\ChangeInterface;

/**
 * Change writer - writes and rolls back {@see \ChangeSet\ChangeInterface} into a given object
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
interface ChangeWriterInterface
{
    /**
     * Write the provided changes to an object
     *
     * @param \ChangeSet\ChangeInterface $change
     * @param object                     $object
     *
     * @return object
     */
    public function write(ChangeInterface $change, $object);

    /**
     * Revert the provided change
     *
     * @param \ChangeSet\ChangeInterface $change
     *
     * @return object
     */
    public function revert(ChangeInterface $change);
}