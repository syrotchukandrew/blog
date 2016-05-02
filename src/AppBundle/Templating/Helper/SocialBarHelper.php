<?php

namespace AppBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;

class SocialBarHelper extends Helper
{
    protected $templating;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function facebookButton($parameters)
    {
        return $this->templating->render('helper/facebookButton.html.twig', $parameters);
    }

    public function getName()
    {
        return 'socialButtons';
    }
}