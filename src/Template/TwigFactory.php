<?php

declare(strict_types=1);

namespace Pulsar\Framework\Template;

use Pulsar\Framework\Session\SessionInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Symfony\Component\Asset\PackageInterface;

class TwigFactory
{
    public function __construct(
        private SessionInterface $session,
        private string $templatePath,
        private PackageInterface $package
    )
    {
    }

    public function create(): Environment
    {
        // instantiate FileSystemLoader with templates path
        $loader = new FilesystemLoader($this->templatePath);

         // instantiate Twig Environment with loader
         $twig = new Environment($loader, [
            'debug' => true,
            'cache' => false
         ]);

         // add new twig session() function to Environment:w
         $twig->addExtension(new DebugExtension());
         $twig->addFunction(new TwigFunction('session', [$this, 'getSession']));
         $twig->addFunction(new TwigFunction('asset', [$this, 'getAsset']));

         return $twig;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getAsset(string $path): string
    {
        return $this->package->getUrl($path);
    }
}