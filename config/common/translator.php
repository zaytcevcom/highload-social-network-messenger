<?php

declare(strict_types=1);

use Middlewares\ContentLanguage;
use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

return [
    TranslatorInterface::class => DI\get(Translator::class),

    Translator::class => static function (ContainerInterface $container): Translator {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{lang:string,resources:array<string[]>} $config
         */
        $config = $container->get('config')['translator'];

        $translator = new Translator($config['lang']);
        $translator->addLoader('php', new PhpFileLoader());
        $translator->addLoader('xlf', new XliffFileLoader());

        /**
         * @psalm-suppress MixedArrayAccess
         * @var array<string> $locales
         */
        $locales = $container->get('config')['locales']['allowed'];

        $modules = [
            'identity',
            'oauth',
            'data',
            'notifier',
        ];

        foreach ($modules as $module) {
            foreach ($locales as $locale) {
                $filename =  __DIR__ . '/../../src/Modules/' . ucfirst($module) . '/Translations/' . $module . '.' . $locale . '.php';

                if (!file_exists($filename)) {
                    $filename =  __DIR__ . '/../../src/Modules/' . ucfirst($module) . '/Translations/' . $module . '.en.php';

                    if (!file_exists($filename)) {
                        continue;
                    }
                }

                $config['resources'][] = [
                    'php',
                    $filename,
                    $locale,
                    $module,
                ];
            }
        }

        foreach ($config['resources'] as $resource) {
            $translator->addResource(...$resource);
        }

        return $translator;
    },

    ContentLanguage::class => static function (ContainerInterface $container): ContentLanguage {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{allowed:string[]} $config
         */
        $config = $container->get('config')['locales'];

        return new ContentLanguage($config['allowed']);
    },

    'config' => [
        'translator' => [
            'lang' => 'en',
            'resources' => [
                [
                    'xlf',
                    __DIR__ . '/../../vendor/symfony/validator/Resources/translations/validators.ru.xlf',
                    'en',
                    'validators',
                ],
                [
                    'xlf',
                    __DIR__ . '/../../vendor/symfony/validator/Resources/translations/validators.ru.xlf',
                    'ru',
                    'validators',
                ],
            ],
        ],
        'locales' => [
            'allowed' => ['ru', 'en', 'af', 'sq', 'am', 'ar', 'hy', 'az', 'eu', 'be', 'bn', 'bs', 'bg', 'my', 'ca', 'ceb', 'ny', 'co', 'hr', 'cs', 'da', 'nl', 'eo', 'et', 'fil', 'fi', 'fr', 'fy', 'gl', 'ka', 'de', 'el', 'gu', 'ht', 'ha', 'haw', 'hi', 'hmn', 'hu', 'is', 'ig', 'id', 'ga', 'it', 'ja', 'kn', 'kk', 'km', 'ko', 'ku', 'ky', 'lo', 'la', 'lv', 'lt', 'lb', 'mk', 'mg', 'ms', 'ml', 'mt', 'mi', 'mr', 'mn', 'ne', 'no', 'ps', 'fa', 'pl', 'pt', 'pa', 'ro', 'sm', 'gd', 'sr', 'st', 'sn', 'sd', 'si', 'sk', 'sl', 'so', 'es', 'su', 'sw', 'sv', 'tl', 'tg', 'ta', 'tt', 'te', 'th', 'tr', 'tk', 'uk', 'ur', 'uz', 'vi', 'cy', 'xh', 'yi', 'yo', 'zu', 'he', 'or', 'rw', 'ug', 'zh-Hans', 'zh-Hant', 'jv'],
        ],
    ],
];
