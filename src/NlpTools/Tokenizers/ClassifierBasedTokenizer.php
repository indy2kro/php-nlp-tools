<?php

declare(strict_types=1);

namespace NlpTools\Tokenizers;

use NlpTools\Classifiers\ClassifierInterface;
use NlpTools\Tokenizers\TokenizerInterface;
use NlpTools\Documents\WordDocument;

/**
 * A tokenizer that uses a classifier (of any type) to determine if
 * there is an "end of word" (EOW). It takes as a parameter an initial
 * tokenizer and then determines if any two following tokens should in
 * fact be one token.
 *
 * Those tokenizers could be nested to produce sentence tokenizers.
 *
 * Example:
 *
 * If we were for example to tokenize the following sentence
 * "Me and O'Brien, we 'll go!" and we used a simple space tokenizer we
 * would end up with this
 * ["Me","and","O'Brien,","we","'ll","go!"]
 * if we used a space and punctuation tokenizer we 'd end up with
 * ["Me","and","O","'","Brien",",","we","'","ll","go","!"]
 * but we want
 * ["Me","and","O'Brien",",","we","'ll","go","!"]
 * so we should train a classifier to do the following
 *
 * Token | Cls
 * ------------
 * Me    | EOW
 * and   | EOW
 * O     | O
 * '     | O
 * Brien | EOW
 * ,     | EOW
 * we    | EOW
 * '     | O
 * ll    | EOW
 * go    | EOW
 * !     | EOW
 *
 */
class ClassifierBasedTokenizer implements TokenizerInterface
{
    public const EOW = 'EOW';

    /**
     * @var array<int, string>
     */
    protected static array $classSet = ['O', 'EOW'];

    // initial tokenizer
    protected TokenizerInterface $tok;

    public function __construct(protected ClassifierInterface $classifier, ?TokenizerInterface $tokenizer = null, protected string $sep = ' ')
    {
        $this->tok = $tokenizer == null ? new WhitespaceAndPunctuationTokenizer() : $tokenizer;
    }

    /**
     * Tokenize the string.
     *
     * 1. Break up the string in tokens using the initial tokenizer
     * 2. Classify each token if it is an EOW
     * 3. For each token that is not an EOW add it to the next EOW token using a separator
     *
     * @param  string $str The character sequence to be broken in tokens
     * @return array<int, mixed>  The token array
     */
    public function tokenize(string $str): array
    {
        // split the string in tokens and create documents to be
        // classified
        $tokens = $this->tok->tokenize($str);
        $docs = [];
        foreach (array_keys($tokens) as $offset) {
            $docs[] = new WordDocument($tokens, $offset, 5);
        }

        // classify each token as an EOW or O
        $tags = [];
        foreach ($docs as $doc) {
            $tags[] = $this->classifier->classify(self::$classSet, $doc);
        }

        // merge O and EOW into real tokens
        $realtokens = [];
        $currentToken = [];
        foreach ($tokens as $offset => $tok) {
            $currentToken[] = $tok;
            if ($tags[$offset] === self::EOW) {
                $realtokens[] = implode($this->sep, $currentToken);
                $currentToken = [];
            }
        }

        // return real tokens
        return $realtokens;
    }
}
