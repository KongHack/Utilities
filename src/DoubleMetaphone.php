<?php
namespace GCWorld\Utilities;

/**
 * Class DoubleMetaphone.
 */
class DoubleMetaphone
{
    protected const LEN_2 = 2;
    protected const LEN_3 = 3;
    protected const LEN_4 = 4;
    protected const LEN_5 = 5;
    protected const LEN_6 = 6;

    protected string $original  = '';
    protected string $primary   = '';
    protected string $secondary = '';
    protected int    $length    = 0;
    protected int    $last      = 0;
    protected int    $current   = 0;

    /**
     * DoubleMetaphone constructor.
     *
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->length   = \strlen($string);
        $this->last     = $this->length - 1;
        $this->original = $string.'     ';

        $this->original = \strtoupper($this->original);

        // skip this at beginning of word
        if ($this->doStringAt($this->original, 0, self::LEN_2, [
            'GN',
            'KN',
            'PN',
            'WR',
            'PS',
        ])) {
            ++$this->current;
        }

        // Initial 'X' is pronounced 'Z' e.g. 'Xavier'
        if ('X' == \substr($this->original, 0, 1)) {
            $this->primary   .= 'S'; // 'Z' maps to 'S'
            $this->secondary .= 'S';
            ++$this->current;
        }

        while (\strlen($this->primary) < self::LEN_4 || \strlen($this->secondary) < self::LEN_4) {
            if ($this->current >= $this->length) {
                break;
            }

            switch (\substr($this->original, $this->current, 1)) {
                case 'A':
                case 'E':
                case 'I':
                case 'O':
                case 'U':
                case 'Y':
                    if (0 == $this->current) {
                        // all init vowels now map to 'A'
                        $this->primary   .= 'A';
                        $this->secondary .= 'A';
                    }
                    ++$this->current;

                    break;
                case 'B':
                    // '-mb', e.g. "dumb", already skipped over ...
                    $this->primary   .= 'P';
                    $this->secondary .= 'P';

                    if ('B' == \substr($this->original, $this->current + 1, 1)) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }

                    break;
                case 'Ç':
                    $this->primary   .= 'S';
                    $this->secondary .= 'S';
                    ++$this->current;

                    break;
                case 'C':
                    // various gremanic
                    if (($this->current > 1)
                        && !$this->doIsVowel($this->original, $this->current - self::LEN_2)
                        && $this->doStringAt($this->original, $this->current - 1, self::LEN_3, [
                            'ACH',
                        ])
                        && (('I' != \substr($this->original, $this->current + self::LEN_2, 1))
                            && (('E' != \substr($this->original, $this->current + self::LEN_2, 1))
                                || $this->doStringAt($this->original, $this->current - self::LEN_2, self::LEN_6, [
                                    'BACHER',
                                    'MACHER',
                                ])
                            )
                        )
                    ) {
                        $this->primary   .= 'K';
                        $this->secondary .= 'K';
                        $this->current   += self::LEN_2;

                        break;
                    }

                    // special case 'caesar'
                    if ((0 == $this->current)
                        && $this->doStringAt($this->original, $this->current, self::LEN_6, [
                            'CAESAR',
                        ])
                    ) {
                        $this->primary   .= 'S';
                        $this->secondary .= 'S';
                        $this->current   += self::LEN_2;

                        break;
                    }

                    // italian 'chianti'
                    if ($this->doStringAt($this->original, $this->current, self::LEN_4, [
                        'CHIA',
                    ])) {
                        $this->primary   .= 'K';
                        $this->secondary .= 'K';
                        $this->current   += self::LEN_2;

                        break;
                    }

                    if ($this->doStringAt($this->original, $this->current, self::LEN_2, [
                        'CH',
                    ])) {
                        // find 'michael'
                        if (($this->current > 0)
                            && $this->doStringAt($this->original, $this->current, self::LEN_4, [
                                'CHAE',
                            ])
                        ) {
                            $this->primary   .= 'K';
                            $this->secondary .= 'X';
                            $this->current   += self::LEN_2;

                            break;
                        }

                        // greek roots e.g. 'chemistry', 'chorus'
                        if ((0 == $this->current)
                            && ($this->doStringAt(
                                    $this->original,
                                    $this->current + 1,
                                    self::LEN_5,
                                    [
                                        'HARAC',
                                        'HARIS',
                                    ]
                                )
                                || $this->doStringAt(
                                    $this->original,
                                    $this->current + 1,
                                    self::LEN_3,
                                    [
                                        'HOR',
                                        'HYM',
                                        'HIA',
                                        'HEM',
                                    ]
                                ))
                            && !$this->doStringAt(
                                $this->original,
                                0,
                                self::LEN_5,
                                [
                                    'CHORE',
                                ]
                            )) {
                            $this->primary   .= 'K';
                            $this->secondary .= 'K';
                            $this->current   += self::LEN_2;

                            break;
                        }

                        // germanic, greek, or otherwise 'ch' for 'kh' sound
                        if (($this->doStringAt(
                                    $this->original,
                                    0,
                                    self::LEN_4,
                                    [
                                        'VAN ',
                                        'VON ',
                                    ]
                                )
                                || $this->doStringAt(
                                    $this->original,
                                    0,
                                    self::LEN_3,
                                    [
                                        'SCH',
                                    ]
                                )) // 'architect' but not 'arch', orchestra', 'orchid'
                            || $this->doStringAt(
                                $this->original,
                                $this->current - self::LEN_2,
                                self::LEN_6,
                                [
                                    'ORCHES',
                                    'ARCHIT',
                                    'ORCHID',
                                ]
                            )
                            || $this->doStringAt(
                                $this->original,
                                $this->current + self::LEN_2,
                                1,
                                [
                                    'T',
                                    'S',
                                ]
                            )
                            || (($this->doStringAt(
                                        $this->original,
                                        $this->current - 1,
                                        1,
                                        [
                                            'A',
                                            'O',
                                            'U',
                                            'E',
                                        ]
                                    )
                                    || (0 == $this->current)) // e.g. 'wachtler', 'weschsler', but not 'tichner'
                                && $this->doStringAt(
                                    $this->original,
                                    $this->current + self::LEN_2,
                                    1,
                                    [
                                        'L',
                                        'R',
                                        'N',
                                        'M',
                                        'B',
                                        'H',
                                        'F',
                                        'V',
                                        'W',
                                        ' ',
                                    ]
                                ))) {
                            $this->primary   .= 'K';
                            $this->secondary .= 'K';
                        } else {
                            if ($this->current > 0) {
                                if ($this->doStringAt(
                                    $this->original,
                                    0,
                                    self::LEN_2,
                                    [
                                        'MC',
                                    ]
                                )) {
                                    // e.g. 'McHugh'
                                    $this->primary   .= 'K';
                                    $this->secondary .= 'K';
                                } else {
                                    $this->primary   .= 'X';
                                    $this->secondary .= 'K';
                                }
                            } else {
                                $this->primary   .= 'X';
                                $this->secondary .= 'X';
                            }
                        }
                        $this->current += self::LEN_2;

                        break;
                    }

                    // e.g. 'czerny'
                    if ($this->doStringAt(
                            $this->original,
                            $this->current,
                            self::LEN_2,
                            [
                                'CZ',
                            ]
                        )
                        && !$this->doStringAt(
                            $this->original,
                            $this->current - self::LEN_2,
                            self::LEN_4,
                            [
                                'WICZ',
                            ]
                        )) {
                        $this->primary   .= 'S';
                        $this->secondary .= 'X';
                        $this->current   += self::LEN_2;

                        break;
                    }

                    // e.g. 'focaccia'
                    if ($this->doStringAt(
                        $this->original,
                        $this->current + 1,
                        self::LEN_3,
                        [
                            'CIA',
                        ]
                    )) {
                        $this->primary   .= 'X';
                        $this->secondary .= 'X';
                        $this->current   += self::LEN_3;

                        break;
                    }

                    // double 'C', but not McClellan'
                    if ($this->doStringAt(
                            $this->original,
                            $this->current,
                            self::LEN_2,
                            [
                                'CC',
                            ]
                        )
                        && !((1 == $this->current) && ('M' == \substr($this->original, 0, 1)))) {
                        // 'bellocchio' but not 'bacchus'
                        if ($this->doStringAt(
                                $this->original,
                                $this->current + self::LEN_2,
                                1,
                                [
                                    'I',
                                    'E',
                                    'H',
                                ]
                            )
                            && !$this->doStringAt(
                                $this->original,
                                $this->current + self::LEN_2,
                                self::LEN_2,
                                [
                                    'HU',
                                ]
                            )) {
                            // 'accident', 'accede', 'succeed'
                            if (((1 == $this->current) && ('A' == \substr($this->original, $this->current - 1, 1)))
                                || $this->doStringAt(
                                    $this->original,
                                    $this->current - 1,
                                    self::LEN_5,
                                    [
                                        'UCCEE',
                                        'UCCES',
                                    ]
                                )) {
                                $this->primary   .= 'KS';
                                $this->secondary .= 'KS';
                                // 'bacci', 'bertucci', other italian
                            } else {
                                $this->primary   .= 'X';
                                $this->secondary .= 'X';
                            }
                            $this->current += self::LEN_3;

                            break;
                        }
                        // Pierce's rule
                        $this->primary   .= 'K';
                        $this->secondary .= 'K';
                        $this->current   += self::LEN_2;

                        break;
                    }

                    if ($this->doStringAt(
                        $this->original,
                        $this->current,
                        self::LEN_2,
                        [
                            'CK',
                            'CG',
                            'CQ',
                        ]
                    )) {
                        $this->primary   .= 'K';
                        $this->secondary .= 'K';
                        $this->current   += self::LEN_2;

                        break;
                    }

                    if ($this->doStringAt(
                        $this->original,
                        $this->current,
                        self::LEN_2,
                        [
                            'CI',
                            'CE',
                            'CY',
                        ]
                    )) {
                        // italian vs. english
                        if ($this->doStringAt(
                            $this->original,
                            $this->current,
                            self::LEN_3,
                            [
                                'CIO',
                                'CIE',
                                'CIA',
                            ]
                        )) {
                            $this->primary   .= 'S';
                            $this->secondary .= 'X';
                        } else {
                            $this->primary   .= 'S';
                            $this->secondary .= 'S';
                        }
                        $this->current += self::LEN_2;

                        break;
                    }

                    // else
                    $this->primary   .= 'K';
                    $this->secondary .= 'K';

                    // name sent in 'mac caffrey', 'mac gregor'
                    if ($this->doStringAt(
                        $this->original,
                        $this->current + 1,
                        self::LEN_2,
                        [
                            ' C',
                            ' Q',
                            ' G',
                        ]
                    )) {
                        $this->current += self::LEN_3;
                    } else {
                        if ($this->doStringAt(
                                $this->original,
                                $this->current + 1,
                                1,
                                [
                                    'C',
                                    'K',
                                    'Q',
                                ]
                            )
                            && !$this->doStringAt(
                                $this->original,
                                $this->current + 1,
                                self::LEN_2,
                                [
                                    'CE',
                                    'CI',
                                ]
                            )) {
                            $this->current += self::LEN_2;
                        } else {
                            ++$this->current;
                        }
                    }

                    break;
                case 'D':
                    if ($this->doStringAt(
                        $this->original,
                        $this->current,
                        self::LEN_2,
                        [
                            'DG',
                        ]
                    )) {
                        if ($this->doStringAt(
                            $this->original,
                            $this->current + self::LEN_2,
                            1,
                            [
                                'I',
                                'E',
                                'Y',
                            ]
                        )) {
                            // e.g. 'edge'
                            $this->primary   .= 'J';
                            $this->secondary .= 'J';
                            $this->current   += self::LEN_3;

                            break;
                        }
                        // e.g. 'edgar'
                        $this->primary   .= 'TK';
                        $this->secondary .= 'TK';
                        $this->current   += self::LEN_2;

                        break;
                    }

                    if ($this->doStringAt(
                        $this->original,
                        $this->current,
                        self::LEN_2,
                        [
                            'DT',
                            'DD',
                        ]
                    )) {
                        $this->primary   .= 'T';
                        $this->secondary .= 'T';
                        $this->current   += self::LEN_2;

                        break;
                    }

                    // else
                    $this->primary   .= 'T';
                    $this->secondary .= 'T';
                    ++$this->current;

                    break;
                case 'F':
                    if ('F' == \substr($this->original, $this->current + 1, 1)) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }
                    $this->primary   .= 'F';
                    $this->secondary .= 'F';

                    break;
                case 'G':
                    if ('H' == \substr($this->original, $this->current + 1, 1)) {
                        if (($this->current > 0) && !$this->doIsVowel($this->original, $this->current - 1)) {
                            $this->primary   .= 'K';
                            $this->secondary .= 'K';
                            $this->current   += self::LEN_2;

                            break;
                        }

                        if ($this->current < self::LEN_3) {
                            // 'ghislane', 'ghiradelli'
                            if (0 == $this->current) {
                                if ('I' == \substr($this->original, $this->current + self::LEN_2, 1)) {
                                    $this->primary   .= 'J';
                                    $this->secondary .= 'J';
                                } else {
                                    $this->primary   .= 'K';
                                    $this->secondary .= 'K';
                                }
                                $this->current += self::LEN_2;

                                break;
                            }
                        }

                        // Parker's rule (with some further refinements) - e.g. 'hugh'
                        if ((($this->current > 1)
                                && $this->doStringAt(
                                    $this->original,
                                    $this->current - self::LEN_2,
                                    1,
                                    [
                                        'B',
                                        'H',
                                        'D',
                                    ]
                                )) // e.g. 'bough'
                            || (($this->current > self::LEN_2)
                                && $this->doStringAt(
                                    $this->original,
                                    $this->current - self::LEN_3,
                                    1,
                                    [
                                        'B',
                                        'H',
                                        'D',
                                    ]
                                )) // e.g. 'broughton'
                            || (($this->current > self::LEN_3)
                                && $this->doStringAt(
                                    $this->original,
                                    $this->current - self::LEN_4,
                                    1,
                                    [
                                        'B',
                                        'H',
                                    ]
                                ))) {
                            $this->current += self::LEN_2;

                            break;
                        }
                        // e.g. 'laugh', 'McLaughlin', 'cough', 'gough', 'rough', 'tough'
                        if (($this->current > self::LEN_2) && ('U' == \substr($this->original, $this->current - 1, 1))
                            && $this->doStringAt(
                                $this->original,
                                $this->current - self::LEN_3,
                                1,
                                [
                                    'C',
                                    'G',
                                    'L',
                                    'R',
                                    'T',
                                ]
                            )) {
                            $this->primary   .= 'F';
                            $this->secondary .= 'F';
                        } elseif (($this->current > 0) // @phpstan-ignore-line
                            && 'I' != \substr($this->original, $this->current - 1, 1)
                        ) {
                            $this->primary   .= 'K';
                            $this->secondary .= 'K';
                        }
                        $this->current += self::LEN_2;

                        break;
                    }

                    if ('N' == \substr($this->original, $this->current + 1, 1)) {
                        if ((1 == $this->current) && $this->doIsVowel($this->original, 0)
                            && !$this->doSlavoGermanic($this->original)) {
                            $this->primary   .= 'KN';
                            $this->secondary .= 'N';
                        } else {
                            // not e.g. 'cagney'
                            if (!$this->doStringAt(
                                    $this->original,
                                    $this->current + self::LEN_2,
                                    self::LEN_2,
                                    [
                                        'EY',
                                    ]
                                )
                                && ('Y' != \substr($this->original, $this->current + 1))
                                && !$this->doSlavoGermanic($this->original)) {
                                $this->primary   .= 'N';
                                $this->secondary .= 'KN';
                            } else {
                                $this->primary   .= 'KN';
                                $this->secondary .= 'KN';
                            }
                        }
                        $this->current += self::LEN_2;

                        break;
                    }

                    // 'tagliaro'
                    if ($this->doStringAt(
                            $this->original,
                            $this->current + 1,
                            self::LEN_2,
                            [
                                'LI',
                            ]
                        )
                        && !$this->doSlavoGermanic($this->original)) {
                        $this->primary   .= 'KL';
                        $this->secondary .= 'L';
                        $this->current   += self::LEN_2;

                        break;
                    }

                    // -ges-, -gep-, -gel- at beginning
                    if ((0 == $this->current)
                        && (('Y' == \substr($this->original, $this->current + 1, 1))
                            || $this->doStringAt(
                                $this->original,
                                $this->current + 1,
                                self::LEN_2,
                                [
                                    'ES',
                                    'EP',
                                    'EB',
                                    'EL',
                                    'EY',
                                    'IB',
                                    'IL',
                                    'IN',
                                    'IE',
                                    'EI',
                                    'ER',
                                ]
                            ))) {
                        $this->primary   .= 'K';
                        $this->secondary .= 'J';
                        $this->current   += self::LEN_2;

                        break;
                    }

                    // -ger-, -gy-
                    if (($this->doStringAt(
                                $this->original,
                                $this->current + 1,
                                self::LEN_2,
                                [
                                    'ER',
                                ]
                            )
                            || ('Y' == \substr($this->original, $this->current + 1, 1)))
                        && !$this->doStringAt(
                            $this->original,
                            0,
                            self::LEN_6,
                            [
                                'DANGER',
                                'RANGER',
                                'MANGER',
                            ]
                        )
                        && !$this->doStringAt(
                            $this->original,
                            $this->current - 1,
                            1,
                            [
                                'E',
                                'I',
                            ]
                        )
                        && !$this->doStringAt(
                            $this->original,
                            $this->current - 1,
                            self::LEN_3,
                            [
                                'RGY',
                                'OGY',
                            ]
                        )) {
                        $this->primary   .= 'K';
                        $this->secondary .= 'J';
                        $this->current   += self::LEN_2;

                        break;
                    }

                    // italian e.g. 'biaggi'
                    if ($this->doStringAt(
                            $this->original,
                            $this->current + 1,
                            1,
                            [
                                'E',
                                'I',
                                'Y',
                            ]
                        )
                        || $this->doStringAt(
                            $this->original,
                            $this->current - 1,
                            self::LEN_4,
                            [
                                'AGGI',
                                'OGGI',
                            ]
                        )) {
                        // obvious germanic
                        if (($this->doStringAt(
                                    $this->original,
                                    0,
                                    self::LEN_4,
                                    [
                                        'VAN ',
                                        'VON ',
                                    ]
                                )
                                || $this->doStringAt(
                                    $this->original,
                                    0,
                                    self::LEN_3,
                                    [
                                        'SCH',
                                    ]
                                ))
                            || $this->doStringAt(
                                $this->original,
                                $this->current + 1,
                                self::LEN_2,
                                [
                                    'ET',
                                ]
                            )) {
                            $this->primary   .= 'K';
                            $this->secondary .= 'K';
                        } else {
                            // always soft if french ending
                            if ($this->doStringAt(
                                $this->original,
                                $this->current + 1,
                                self::LEN_4,
                                [
                                    'IER ',
                                ]
                            )) {
                                $this->primary   .= 'J';
                                $this->secondary .= 'J';
                            } else {
                                $this->primary   .= 'J';
                                $this->secondary .= 'K';
                            }
                        }
                        $this->current += self::LEN_2;

                        break;
                    }

                    if ('G' == \substr($this->original, $this->current + 1, 1)) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }

                    $this->primary   .= 'K';
                    $this->secondary .= 'K';

                    break;
                case 'H':
                    // only keep if first & before vowel or btw. self::LEN_2 vowels
                    if (((0 == $this->current) || $this->doIsVowel($this->original, $this->current - 1))
                        && $this->doIsVowel($this->original, $this->current + 1)) {
                        $this->primary   .= 'H';
                        $this->secondary .= 'H';
                        $this->current   += self::LEN_2;
                    } else {
                        ++$this->current;
                    }

                    break;
                case 'J':
                    // obvious spanish, 'jose', 'san jacinto'
                    if ($this->doStringAt(
                            $this->original,
                            $this->current,
                            self::LEN_4,
                            [
                                'JOSE',
                            ]
                        )
                        || $this->doStringAt(
                            $this->original,
                            0,
                            self::LEN_4,
                            [
                                'SAN ',
                            ]
                        )) {
                        if (((0 == $this->current) && (' ' == \substr($this->original, $this->current + self::LEN_4, 1)))
                            || $this->doStringAt(
                                $this->original,
                                0,
                                self::LEN_4,
                                [
                                    'SAN ',
                                ]
                            )) {
                            $this->primary   .= 'H';
                            $this->secondary .= 'H';
                        } else {
                            $this->primary   .= 'J';
                            $this->secondary .= 'H';
                        }
                        ++$this->current;

                        break;
                    }

                    if ((0 == $this->current)
                        /* @phpstan-ignore-next-line */
                        && !$this->doStringAt($this->original, $this->current, self::LEN_4, ['JOSE'])
                    ) {
                        $this->primary   .= 'J'; // Yankelovich/Jankelowicz
                        $this->secondary .= 'A';
                    } else {
                        // spanish pron. of .e.g. 'bajador'
                        if ($this->doIsVowel($this->original, $this->current - 1)
                            && !$this->doSlavoGermanic($this->original)
                            && (('A' == \substr($this->original, $this->current + 1, 1))
                                || ('O' == \substr(
                                        $this->original,
                                        $this->current + 1,
                                        1
                                    )))) {
                            $this->primary   .= 'J';
                            $this->secondary .= 'H';
                        } else {
                            if ($this->current == $this->last) {
                                $this->primary   .= 'J';
                                $this->secondary .= '';
                            } else {
                                if (!$this->doStringAt(
                                        $this->original,
                                        $this->current + 1,
                                        1,
                                        [
                                            'L',
                                            'T',
                                            'K',
                                            'S',
                                            'N',
                                            'M',
                                            'B',
                                            'Z',
                                        ]
                                    )
                                    && !$this->doStringAt(
                                        $this->original,
                                        $this->current - 1,
                                        1,
                                        [
                                            'S',
                                            'K',
                                            'L',
                                        ]
                                    )) {
                                    $this->primary   .= 'J';
                                    $this->secondary .= 'J';
                                }
                            }
                        }
                    }

                    if ('J' == \substr($this->original, $this->current + 1, 1)) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }

                    break;
                case 'K':
                    if ('K' == \substr($this->original, $this->current + 1, 1)) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }
                    $this->primary   .= 'K';
                    $this->secondary .= 'K';

                    break;
                case 'L':
                    if ('L' == \substr($this->original, $this->current + 1, 1)) {
                        // spanish e.g. 'cabrillo', 'gallegos'
                        if ((($this->current == ($this->length - self::LEN_3))
                                && $this->doStringAt(
                                    $this->original,
                                    $this->current - 1,
                                    self::LEN_4,
                                    [
                                        'ILLO',
                                        'ILLA',
                                        'ALLE',
                                    ]
                                ))
                            || (($this->doStringAt(
                                        $this->original,
                                        $this->last - 1,
                                        self::LEN_2,
                                        [
                                            'AS',
                                            'OS',
                                        ]
                                    )
                                    || $this->doStringAt(
                                        $this->original,
                                        $this->last,
                                        1,
                                        [
                                            'A',
                                            'O',
                                        ]
                                    ))
                                && $this->doStringAt(
                                    $this->original,
                                    $this->current - 1,
                                    self::LEN_4,
                                    [
                                        'ALLE',
                                    ]
                                ))) {
                            $this->primary   .= 'L';
                            $this->secondary .= '';
                            $this->current   += self::LEN_2;

                            break;
                        }
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }
                    $this->primary   .= 'L';
                    $this->secondary .= 'L';

                    break;
                case 'M':
                    if (($this->doStringAt(
                                $this->original,
                                $this->current - 1,
                                self::LEN_3,
                                [
                                    'UMB',
                                ]
                            )
                            && ((($this->current + 1) == $this->last)
                                || $this->doStringAt(
                                    $this->original,
                                    $this->current + self::LEN_2,
                                    self::LEN_2,
                                    [
                                        'ER',
                                    ]
                                ))) // 'dumb', 'thumb'
                        || ('M' == \substr($this->original, $this->current + 1, 1))) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }
                    $this->primary   .= 'M';
                    $this->secondary .= 'M';

                    break;
                case 'N':
                    if ('N' == \substr($this->original, $this->current + 1, 1)) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }
                    $this->primary   .= 'N';
                    $this->secondary .= 'N';

                    break;
                case 'Ñ':
                    ++$this->current;
                    $this->primary   .= 'N';
                    $this->secondary .= 'N';

                    break;
                case 'P':
                    if ('H' == \substr($this->original, $this->current + 1, 1)) {
                        $this->current   += self::LEN_2;
                        $this->primary   .= 'F';
                        $this->secondary .= 'F';

                        break;
                    }

                    // also account for "campbell" and "raspberry"
                    if ($this->doStringAt(
                        $this->original,
                        $this->current + 1,
                        1,
                        [
                            'P',
                            'B',
                        ]
                    )) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }
                    $this->primary   .= 'P';
                    $this->secondary .= 'P';

                    break;
                case 'Q':
                    if ('Q' == \substr($this->original, $this->current + 1, 1)) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }
                    $this->primary   .= 'K';
                    $this->secondary .= 'K';

                    break;
                case 'R':
                    // french e.g. 'rogier', but exclude 'hochmeier'
                    if (($this->current == $this->last) && !$this->doSlavoGermanic($this->original)
                        && $this->doStringAt(
                            $this->original,
                            $this->current - self::LEN_2,
                            self::LEN_2,
                            [
                                'IE',
                            ]
                        )
                        && !$this->doStringAt(
                            $this->original,
                            $this->current - self::LEN_4,
                            self::LEN_2,
                            [
                                'ME',
                                'MA',
                            ]
                        )) {
                        $this->primary   .= '';
                        $this->secondary .= 'R';
                    } else {
                        $this->primary   .= 'R';
                        $this->secondary .= 'R';
                    }
                    if ('R' == \substr($this->original, $this->current + 1, 1)) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }

                    break;
                case 'S':
                    // special cases 'island', 'isle', 'carlisle', 'carlysle'
                    if ($this->doStringAt(
                        $this->original,
                        $this->current - 1,
                        self::LEN_3,
                        [
                            'ISL',
                            'YSL',
                        ]
                    )) {
                        ++$this->current;

                        break;
                    }

                    // special case 'sugar-'
                    if ((0 == $this->current)
                        && $this->doStringAt(
                            $this->original,
                            $this->current,
                            self::LEN_5,
                            [
                                'SUGAR',
                            ]
                        )) {
                        $this->primary   .= 'X';
                        $this->secondary .= 'S';
                        ++$this->current;

                        break;
                    }

                    if ($this->doStringAt(
                        $this->original,
                        $this->current,
                        self::LEN_2,
                        [
                            'SH',
                        ]
                    )) {
                        // germanic
                        if ($this->doStringAt(
                            $this->original,
                            $this->current + 1,
                            self::LEN_4,
                            [
                                'HEIM',
                                'HOEK',
                                'HOLM',
                                'HOLZ',
                            ]
                        )) {
                            $this->primary   .= 'S';
                            $this->secondary .= 'S';
                        } else {
                            $this->primary   .= 'X';
                            $this->secondary .= 'X';
                        }
                        $this->current += self::LEN_2;

                        break;
                    }

                    // italian & armenian
                    if ($this->doStringAt(
                            $this->original,
                            $this->current,
                            self::LEN_3,
                            [
                                'SIO',
                                'SIA',
                            ]
                        )
                        || $this->doStringAt(
                            $this->original,
                            $this->current,
                            self::LEN_4,
                            [
                                'SIAN',
                            ]
                        )) {
                        if (!$this->doSlavoGermanic($this->original)) {
                            $this->primary   .= 'S';
                            $this->secondary .= 'X';
                        } else {
                            $this->primary   .= 'S';
                            $this->secondary .= 'S';
                        }
                        $this->current += self::LEN_3;

                        break;
                    }

                    // german & anglicisations, e.g. 'smith' match 'schmidt', 'snider' match 'schneider'
                    // also, -sz- in slavic language altho in hungarian it is pronounced 's'
                    if (((0 == $this->current)
                            && $this->doStringAt(
                                $this->original,
                                $this->current + 1,
                                1,
                                [
                                    'M',
                                    'N',
                                    'L',
                                    'W',
                                ]
                            ))
                        || $this->doStringAt(
                            $this->original,
                            $this->current + 1,
                            1,
                            [
                                'Z',
                            ]
                        )) {
                        $this->primary   .= 'S';
                        $this->secondary .= 'X';
                        if ($this->doStringAt(
                            $this->original,
                            $this->current + 1,
                            1,
                            [
                                'Z',
                            ]
                        )) {
                            $this->current += self::LEN_2;
                        } else {
                            ++$this->current;
                        }

                        break;
                    }

                    if ($this->doStringAt(
                        $this->original,
                        $this->current,
                        self::LEN_2,
                        [
                            'SC',
                        ]
                    )) {
                        // Schlesinger's rule
                        if ('H' == \substr($this->original, $this->current + self::LEN_2, 1)) {
                            // dutch origin, e.g. 'school', 'schooner'
                            if ($this->doStringAt(
                                $this->original,
                                $this->current + self::LEN_3,
                                self::LEN_2,
                                [
                                    'OO',
                                    'ER',
                                    'EN',
                                    'UY',
                                    'ED',
                                    'EM',
                                ]
                            )) {
                                // 'schermerhorn', 'schenker'
                                if ($this->doStringAt(
                                    $this->original,
                                    $this->current + self::LEN_3,
                                    self::LEN_2,
                                    [
                                        'ER',
                                        'EN',
                                    ]
                                )) {
                                    $this->primary   .= 'X';
                                    $this->secondary .= 'SK';
                                } else {
                                    $this->primary   .= 'SK';
                                    $this->secondary .= 'SK';
                                }
                                $this->current += self::LEN_3;

                                break;
                            }
                            if ((0 == $this->current) && !$this->doIsVowel($this->original, self::LEN_3)
                                && ('W' != \substr($this->original, $this->current + self::LEN_3, 1))) {
                                $this->primary   .= 'X';
                                $this->secondary .= 'S';
                            } else {
                                $this->primary   .= 'X';
                                $this->secondary .= 'X';
                            }
                            $this->current += self::LEN_3;

                            break;
                        }

                        if ($this->doStringAt(
                            $this->original,
                            $this->current + self::LEN_2,
                            1,
                            [
                                'I',
                                'E',
                                'Y',
                            ]
                        )) {
                            $this->primary   .= 'S';
                            $this->secondary .= 'S';
                            $this->current   += self::LEN_3;

                            break;
                        }

                        // else
                        $this->primary   .= 'SK';
                        $this->secondary .= 'SK';
                        $this->current   += self::LEN_3;

                        break;
                    }

                    // french e.g. 'resnais', 'artois'
                    if (($this->current == $this->last)
                        && $this->doStringAt(
                            $this->original,
                            $this->current - self::LEN_2,
                            self::LEN_2,
                            [
                                'AI',
                                'OI',
                            ]
                        )) {
                        $this->primary   .= '';
                        $this->secondary .= 'S';
                    } else {
                        $this->primary   .= 'S';
                        $this->secondary .= 'S';
                    }

                    if ($this->doStringAt(
                        $this->original,
                        $this->current + 1,
                        1,
                        [
                            'S',
                            'Z',
                        ]
                    )) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }

                    break;
                case 'T':
                    if ($this->doStringAt(
                        $this->original,
                        $this->current,
                        self::LEN_4,
                        [
                            'TION',
                        ]
                    )) {
                        $this->primary   .= 'X';
                        $this->secondary .= 'X';
                        $this->current   += self::LEN_3;

                        break;
                    }

                    if ($this->doStringAt(
                        $this->original,
                        $this->current,
                        self::LEN_3,
                        [
                            'TIA',
                            'TCH',
                        ]
                    )) {
                        $this->primary   .= 'X';
                        $this->secondary .= 'X';
                        $this->current   += self::LEN_3;

                        break;
                    }

                    if ($this->doStringAt(
                            $this->original,
                            $this->current,
                            self::LEN_2,
                            [
                                'TH',
                            ]
                        )
                        || $this->doStringAt(
                            $this->original,
                            $this->current,
                            self::LEN_3,
                            [
                                'TTH',
                            ]
                        )) {
                        // special case 'thomas', 'thames' or germanic
                        if ($this->doStringAt(
                                $this->original,
                                $this->current + self::LEN_2,
                                self::LEN_2,
                                [
                                    'OM',
                                    'AM',
                                ]
                            )
                            || $this->doStringAt(
                                $this->original,
                                0,
                                self::LEN_4,
                                [
                                    'VAN ',
                                    'VON ',
                                ]
                            )
                            || $this->doStringAt(
                                $this->original,
                                0,
                                self::LEN_3,
                                [
                                    'SCH',
                                ]
                            )) {
                            $this->primary   .= 'T';
                            $this->secondary .= 'T';
                        } else {
                            $this->primary   .= '0';
                            $this->secondary .= 'T';
                        }
                        $this->current += self::LEN_2;

                        break;
                    }

                    if ($this->doStringAt(
                        $this->original,
                        $this->current + 1,
                        1,
                        [
                            'T',
                            'D',
                        ]
                    )) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }
                    $this->primary   .= 'T';
                    $this->secondary .= 'T';

                    break;
                case 'V':
                    if ('V' == \substr($this->original, $this->current + 1, 1)) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }
                    $this->primary   .= 'F';
                    $this->secondary .= 'F';

                    break;
                case 'W':
                    // can also be in middle of word
                    if ($this->doStringAt(
                        $this->original,
                        $this->current,
                        self::LEN_2,
                        [
                            'WR',
                        ]
                    )) {
                        $this->primary   .= 'R';
                        $this->secondary .= 'R';
                        $this->current   += self::LEN_2;

                        break;
                    }

                    if ((0 == $this->current)
                        && ($this->doIsVowel($this->original, $this->current + 1)
                            || $this->doStringAt(
                                $this->original,
                                $this->current,
                                self::LEN_2,
                                [
                                    'WH',
                                ]
                            ))) {
                        // Wasserman should match Vasserman
                        if ($this->doIsVowel($this->original, $this->current + 1)) {
                            $this->primary   .= 'A';
                            $this->secondary .= 'F';
                        } else {
                            // need Uomo to match Womo
                            $this->primary   .= 'A';
                            $this->secondary .= 'A';
                        }
                    }

                    // Arnow should match Arnoff
                    if ((($this->current == $this->last) && $this->doIsVowel($this->original, $this->current - 1))
                        || $this->doStringAt(
                            $this->original,
                            $this->current - 1,
                            self::LEN_5,
                            [
                                'EWSKI',
                                'EWSKY',
                                'OWSKI',
                                'OWSKY',
                            ]
                        )
                        || $this->doStringAt(
                            $this->original,
                            0,
                            self::LEN_3,
                            [
                                'SCH',
                            ]
                        )) {
                        $this->primary   .= '';
                        $this->secondary .= 'F';
                        ++$this->current;

                        break;
                    }

                    // polish e.g. 'filipowicz'
                    if ($this->doStringAt(
                        $this->original,
                        $this->current,
                        self::LEN_4,
                        [
                            'WICZ',
                            'WITZ',
                        ]
                    )) {
                        $this->primary   .= 'TS';
                        $this->secondary .= 'FX';
                        $this->current   += self::LEN_4;

                        break;
                    }

                    // else skip it
                    ++$this->current;

                    break;
                case 'X':
                    // french e.g. breaux
                    if (!(($this->current == $this->last)
                        && ($this->doStringAt(
                                $this->original,
                                $this->current - self::LEN_3,
                                self::LEN_3,
                                [
                                    'IAU',
                                    'EAU',
                                ]
                            )
                            || $this->doStringAt(
                                $this->original,
                                $this->current - self::LEN_2,
                                self::LEN_2,
                                [
                                    'AU',
                                    'OU',
                                ]
                            )))) {
                        $this->primary   .= 'KS';
                        $this->secondary .= 'KS';
                    }

                    if ($this->doStringAt(
                        $this->original,
                        $this->current + 1,
                        1,
                        [
                            'C',
                            'X',
                        ]
                    )) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }

                    break;
                case 'Z':
                    // chinese pinyin e.g. 'zhao'
                    if ('H' == \substr($this->original, $this->current + 1, 1)) {
                        $this->primary   .= 'J';
                        $this->secondary .= 'J';
                        $this->current   += self::LEN_2;

                        break;
                    }
                    if ($this->doStringAt(
                            $this->original,
                            $this->current + 1,
                            self::LEN_2,
                            [
                                'ZO',
                                'ZI',
                                'ZA',
                            ]
                        )
                        || ($this->doSlavoGermanic($this->original)
                            && (($this->current > 0)
                                && 'T' != \substr(
                                    $this->original,
                                    $this->current - 1,
                                    1
                                )))) {
                        $this->primary   .= 'S';
                        $this->secondary .= 'TS';
                    } else {
                        $this->primary   .= 'S';
                        $this->secondary .= 'S';
                    }

                    if ('Z' == \substr($this->original, $this->current + 1, 1)) {
                        $this->current += self::LEN_2;
                    } else {
                        ++$this->current;
                    }

                    break;
                default:
                    ++$this->current;
            }
        }

        $this->primary   = \substr($this->primary, 0, self::LEN_4);
        $this->secondary = \substr($this->secondary, 0, self::LEN_4);

        $result['primary']   = $this->primary;
        $result['secondary'] = $this->secondary;
    }

    /**
     * @return string
     */
    public function getPrimary()
    {
        return (string) $this->primary;
    }

    /**
     * @return string
     */
    public function getSecondary()
    {
        return (string) $this->secondary;
    }

    /**
     * @param string $string
     * @param int    $start
     * @param int    $length
     * @param array  $list
     *
     * @return int
     */
    protected function doStringAt(string $string, int $start, int $length, array $list)
    {
        if (($start < 0) || ($start >= \strlen($string))) {
            return 0;
        }

        for ($i = 0; $i < \count($list); ++$i) {
            if ($list[$i] == \substr($string, $start, $length)) {
                return 1;
            }
        }

        return 0;
    }

    /**
     * @param string $string
     * @param int    $pos
     *
     * @return int
     */
    protected function doIsVowel(string $string, int $pos)
    {
        return \preg_match('/[AEIOUY]/', \substr($string, $pos, 1));
    }

    /**
     * @param string $string
     *
     * @return int
     */
    protected function doSlavoGermanic(string $string)
    {
        return \preg_match('/W|K|CZ|WITZ/', $string);
    }
}
