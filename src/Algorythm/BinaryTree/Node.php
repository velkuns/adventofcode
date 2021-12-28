<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Trigonometry\BinaryTree;

class Node
{
    public const SIDE_LEFT = 'left';
    public const SIDE_RIGHT = 'right';
    public const SIDE_NONE =  'none';

    protected bool $isLeft = false;
    protected bool $isRight = false;
    private int $numberOfAncestor;
    private array $payload = [];
    private ?Node $parent;
    private ?NodeLeft $left = null;
    private ?NodeRight $right = null;

    public function __construct(?Node $parent = null)
    {
        $this->parent = $parent;

        $this->numberOfAncestor = $this->parent !== null ? $parent->getNumberOfAncestor() + 1 : 0;
    }

    public function concertToRightNode(Node $parent): NodeRight
    {
        $node = new NodeRight($parent);
        $node->setLeft($this->left()->setParent($node));
        $node->setRight($this->right()->setParent($node));
        $node->setPayload($this->getPayload());

        $node->updateNumberOfAncestor();

        return $node;
    }

    public function concertToLeftNode(Node $parent): NodeLeft
    {
        $node = new NodeLeft($parent);
        $node->setLeft($this->left()->setParent($node));
        $node->setRight($this->right()->setParent($node));
        $node->setPayload($this->getPayload());

        $node->updateNumberOfAncestor();

        return $node;
    }

    public function updateNumberOfAncestor(): self
    {
        $nodeLeft  = $this->left();
        $nodeRight = $this->right();

        if ($nodeLeft !== null) {
            $nodeLeft->increaseNumberOfAncestor();
            $nodeLeft->updateNumberOfAncestor();
        }

        if ($nodeRight !== null) {
            $nodeRight->increaseNumberOfAncestor();
            $nodeRight->updateNumberOfAncestor();
        }

        return $this;
    }

    public function setLeft(?NodeLeft $node): self
    {
        $this->left = $node;
        return $this;
    }

    public function setRight(?NodeRight $node): self
    {
        $this->right = $node;
        return $this;
    }

    public function parent(): ?Node
    {
        return $this->parent;
    }

    public function setParent(?Node $node): self
    {
        $this->parent = $node;
        return $this;
    }

    public function left(): ?NodeLeft
    {
        return $this->left;
    }

    public function right(): ?NodeRight
    {
        return $this->right;
    }

    public function side(): string
    {
        if (!$this->isLeft && !$this->isRight) {
            return Node::SIDE_NONE;
        }

        return $this->isLeft ? Node::SIDE_LEFT : Node::SIDE_RIGHT;
    }

    public function setValue(int $value): self
    {
        $this->payload['value'] = $value;
        return $this;
    }

    public function getValue(): int
    {
        return $this->payload['value'] ?? 0;
    }

    public function setPayload(array $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function increaseNumberOfAncestor(): self
    {
        $this->numberOfAncestor++;

        return $this;
    }

    public function getNumberOfAncestor(): int
    {
        return $this->numberOfAncestor;
    }

    public function isLeaf(): bool
    {
        return ($this->left === null && $this->right === null);
    }

    public function isRoot(): bool
    {
        return ($this->parent === null);
    }

    public function isLeft(): bool
    {
        return $this->isLeft;
    }

    public function isRight(): bool
    {
        return $this->isRight;
    }

    public function __toString()
    {
        $id    = spl_object_id($this);

        if ($this->isRoot()) {
            return "<<root:$id>>";
        }

        $side  = ($this->isRight() ? 'r' : 'l' );
        $count = $this->getNumberOfAncestor();
        $value = $this->getValue();

        if ($this->isLeaf()) {
            return "<<leaf:$id|$side|$count|[$value]>>";
        }

        return "<<node:$id|$side|$count|[$value]>>";
    }
}
