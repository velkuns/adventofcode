<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Algorythm\BinaryTree;

/**
 * Class Snailfish
 *
 * @author Romain Cottard
 */
class Snailfish extends BinaryTree
{
    private ?Node $previous = null;
    private ?Node $current = null;

    public static function fromString(string $string): self
    {
        $snailfish = new Snailfish();
        $parent   = $snailfish->root();
        for ($c = 1, $len = strlen($string) - 1; $c < $len; $c++) {
            $char = $string[$c];
            switch ($char) {
                case '[':
                    if ($string[$c-1] === ',') {
                        continue 2; // parent is already a node, skip this step
                    }
                    $node = new NodeLeft($parent);
                    $parent->setLeft($node);
                    $parent = $node;
                    break;
                case ',':
                    if ($string[$c+1] !== '[') {
                        continue 2; // right side will be a leaf, skip new node
                    }
                    $node = new NodeRight($parent);
                    $parent->setRight($node);
                    $parent = $node;
                    break;
                case ']':
                    $parent = $parent->parent();
                    break;
                default:
                    if ($string[$c+1] === ',') {
                        $node = new NodeLeft($parent);
                        $node->setValue((int) $char);
                        $parent->setLeft($node);
                    } else {
                        $node = new NodeRight($parent);
                        $node->setValue((int) $char);
                        $parent->setRight($node);
                    }
                    break;
            }
        }

        return $snailfish;
    }

    public function __toString(): string
    {
        $this->current  = null;
        $this->previous = null;

        $string = '';

        $nodeViewCount = [];
        while (($node = $this->next()) !== null) {
            $nodeViewCount[spl_object_id($node)] = ($nodeViewCount[spl_object_id($node)] ?? 0) + 1;
            $count = $nodeViewCount[spl_object_id($node)];

            if ($node->isLeaf()) {
                $string .= $node->getValue();
                continue;
            }

            if ($count === 1) {
                $string .= '[';
            } elseif ($count === 2) {
                $string .= ',';
            } else {
                $string .= ']';
            }
        }

        return $string;
    }

    public function magnitude(): int
    {
        $this->current  = null;
        $this->previous = null;

        $nodeViewCount = [];
        while (($node = $this->next()) !== null) {
            $nodeViewCount[spl_object_id($node)] = ($nodeViewCount[spl_object_id($node)] ?? 0) + 1;
            $count = $nodeViewCount[spl_object_id($node)];

            if ($node->isLeaf()) {
                $value = $node->getValue() * ($node->isLeft() ? 3 : 2);
                $node->parent()->setValue($value);
                continue;
            }

            if ($count === 3) {
                $value = ($node->left()->getValue() * 3) + ($node->right()->getValue() * 2);
                $node->setValue($value);
            }
        }

        return $this->root()->getValue();
    }

    public function add(Snailfish $snailfish): self
    {
        $root = new Node();

        $left  = $this->root()->concertToLeftNode($root);
        $right = $snailfish->root()->concertToRightNode($root);

        $root->setLeft($left);
        $root->setRight($right);

        $this->setRoot($root);

        return $this;
    }

    public function reduce(): self
    {
        $this->computeLeaves();

        foreach ($this->leaves() as $node) {
            if ($node->isRight()) {
                continue; // Do not double-check (left & right have the same parent)
            }

            if ($node->getNumberOfAncestor() === 5) {
                $this->explodeNode($node->parent());
                unset($node);
                $this->reduce();
                break;
            }
        }

        foreach ($this->leaves() as $node) {
            if ($node->getValue() >= 10) {
                $this->splitNode($node);
                $this->reduce(); // reduce repeat process
                break;
            }
        }

        return $this;
    }

    private function next(): ?Node
    {
        //~ Initialize
        if ($this->current === null) {
            $this->current = $this->root();
            return $this->current;
        }

        //~ We are on leaf, go upward to parent
        if ($this->current->isLeaf()) {
            $this->previous = $this->current;
            $this->current  = $this->current->parent();

            return $this->current;
        }

        //~ Previous node was left side, so go to right side
        if ($this->current->left() === $this->previous) {
            $this->previous = $this->current;
            $this->current  = $this->current->right();

            return $this->current;
        }

        //~ Previous node was right, so go upward to parent
        if ($this->current->right() === $this->previous) {
            $this->previous = $this->current;
            $this->current  = $this->current->parent();

            return $this->current;
        }

        //~ In other case, go downward to left side
        $this->previous = $this->current;
        $this->current  = $this->current->left();

        return $this->current;
    }

    private function explodeNode(Node $node): void
    {
        $nodeLeft = $this->getClosestLeafOnSide($node, 'left');
        if ($nodeLeft !== null) {
            $nodeLeft->setValue($nodeLeft->getValue() + $node->left()->getValue());
        }

        $nodeRight = $this->getClosestLeafOnSide($node, 'right');
        if ($nodeRight !== null) {
            $nodeRight->setValue($nodeRight->getValue() + $node->right()->getValue());
        }

        if ($node->isLeft()) {
            $node->parent()->setLeft(new NodeLeft($node->parent()));
        } else {
            $node->parent()->setRight(new NodeRight($node->parent()));
        }
    }

    private function splitNode(Node $node): void
    {
        $nodeLeft = new NodeLeft($node);
        $nodeLeft->setValue((int) floor($node->getValue() / 2));

        $nodeRight = new NodeRight($node);
        $nodeRight->setValue((int) ceil($node->getValue() / 2));

        $node->setValue(0);
        $node->setLeft($nodeLeft);
        $node->setRight($nodeRight);
    }

    public function getClosestLeafOnSide(Node $node, string $side): ?Node
    {
        $parent   = $node->parent();
        $fromNode = $node;

        //~ Walk in tree upward on the same side until found a new branch on "same" side (or until root with side "none")
        while ($parent !== null && $parent->$side() === $fromNode) {
            $fromNode = $parent;
            $parent   = $parent->parent();
        }

        //~ If we found branch, walk on it on opposite side to get the closest leaf.
        $nodeForSideValue = null;
        if ($parent !== null) {
            $nodeForSideValue = $parent->$side();
            $oppositeSide = $side === Node::SIDE_LEFT ? 'right' : 'left';
            while(!$nodeForSideValue->isLeaf()) {
                $nodeForSideValue = $nodeForSideValue->$oppositeSide(); // continue down until the leaf
            }
        }

        return $nodeForSideValue;
    }
}
