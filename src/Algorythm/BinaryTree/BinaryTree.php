<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Algorythm\BinaryTree;

class BinaryTree
{
    private Node $root;
    private array $leaves = [];

    public function __construct()
    {
        $this->root = new Node();
    }

    public function root(): Node
    {
        return $this->root;
    }

    public function setRoot(Node $root): self
    {
        $this->root = $root;

        return $this;
    }

    /**
     * @return Node[]
     */
    public function leaves(): array
    {
        return $this->leaves;
    }

    public function computeLeaves(?Node $node = null): self
    {
        if ($node === null) {
            $this->leaves = [];
            $node = $this->root;
        }

        $left  = $node->left();
        $right = $node->right();

        if ($left->isLeaf()) {
            $this->leaves[] = $left;
        } else {
            $this->computeLeaves($node->left());
        }

        if ($right->isLeaf()) {
            $this->leaves[] = $right;
        } else {
            $this->computeLeaves($node->right());
        }

        return $this;
    }
}
