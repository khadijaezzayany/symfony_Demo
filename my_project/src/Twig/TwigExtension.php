<?php

namespace App\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\RequestStack;

class TwigExtension extends AbstractExtension
{

    protected $em;
    protected $request;

    public function __construct(Container $container, EntityManagerInterface $em, RequestStack $request_stack)
    {
        $this->container = $container;
        $this->em = $em;
        $this->request = $request_stack->getCurrentRequest();
    }
    public function _env($val)
    {
        return $_ENV[$val];
    }


    public function getFunctions()
    {
        return [
            new TwigFunction('_env', [$this, '_env']),
            new TwigFunction('getGlobalVars', [$this, 'getGlobalVars']),
            new TwigFunction('firstLastPosition', [$this, 'firstLastPosition']),
            new TwigFunction('getCurrentPosition', [$this, 'getCurrentPosition']),
            new TwigFunction('stripTagsFunc', [$this, 'stripTagsFunc']),
        ];
    }


    // get the position above the curent ordre
    public function firstLastPosition($class_name, $filter_field = 'position', $type = 'MIN')
    {
        $result = $this->em->createQueryBuilder()
            ->from($class_name, 'elem')
            ->select($type . '(elem.' . $filter_field . ') AS position');
        if (strpos('_', $filter_field) !== false) {
            $result->join('elem.categorie_thalasso', 'ct')
                ->andWhere('ct.id = :idval')
                ->setParameter('idval', str_replace('position_', '', $filter_field));
        }
        $result = $result->getQuery()
            ->getSingleResult();
        return intval($result['position']);
    }
    // get the current position
    public function getCurrentPosition($class_name, $filter_field = 'position', $objId)
    {
        $result = $this->em->createQueryBuilder()
            ->from($class_name, 'elem')
            ->select('elem.' . $filter_field . ' AS position')
            ->andWhere('elem.id = :objId')
            ->setParameter('objId', $objId)
            ->getQuery()
            ->getSingleResult();
        return intval($result['position']);
    }
    //
    public function stripTagsFunc($text)
    {
        return strip_tags(html_entity_decode($text));
    }
}