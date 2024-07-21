<?php

declare(strict_types=1);

namespace NlpTools\Documents;

use NlpTools\Utils\IdentityTransformer;
use NlpTools\Documents\TokensDocument;
use NlpTools\Documents\TrainingDocument;
use NlpTools\Documents\WordDocument;
use PHPUnit\Framework\TestCase;

class TransformationsTest extends TestCase
{
    public static function provideTokens(): array
    {
        return [[["1", "2", "3", "4", "5", "6", "7"]]];
    }

    /**
     * @dataProvider provideTokens
     */
    public function testTokensDocument(array $tokens): void
    {
        $tokensDocument = new TokensDocument($tokens);
        $identityTransformer = new IdentityTransformer();
        $this->assertEquals(
            $tokens,
            $tokensDocument->getDocumentData()
        );
        $tokensDocument->applyTransformation($identityTransformer);
        $this->assertEquals(
            $tokens,
            $tokensDocument->getDocumentData()
        );

        $trainingDocument = new TrainingDocument("", new TokensDocument($tokens));
        $trainingDocument->applyTransformation($identityTransformer);
        $this->assertEquals(
            $tokens,
            $trainingDocument->getDocumentData()
        );
    }

    /**
     * @dataProvider provideTokens
     */
    public function testWordDocument(array $tokens): void
    {
        $identityTransformer = new IdentityTransformer();
        $wordDocument = new WordDocument($tokens, count($tokens) / 2, 2);
        $correct = $wordDocument->getDocumentData();
        $wordDocument->applyTransformation($identityTransformer);
        $this->assertEquals(
            $correct,
            $wordDocument->getDocumentData()
        );

        $trainingDocument = new TrainingDocument("", new WordDocument($tokens, count($tokens) / 2, 2));
        $trainingDocument->applyTransformation($identityTransformer);
        $this->assertEquals(
            $correct,
            $trainingDocument->getDocumentData()
        );
    }
}
