<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridInterface;



final class ClientAdmin extends AbstractAdmin
{

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('move', $this->getRouterIdParameter().'/move/{position}');
    }


protected function configureDefaultSortValues(array &$sortValues): void
{
    $sortValues[DatagridInterface::PAGE] = 1;
    $sortValues[DatagridInterface::SORT_ORDER] = 'ASC';
    $sortValues[DatagridInterface::SORT_BY] = 'position';
}

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            // ->add('id')
            ->add('name')
            ->add('email')
            ->add('position')
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            // ->add('id')
            ->add('name')
            ->add('email')
            ->add('position')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    'move' => [
                        'template' => '@PixSortableBehavior/Default/_sort.html.twig'
                    ],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            // ->add('id')
            ->add('name')
            ->add('email')
            ->add('position')
            ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            // ->add('id')
            ->add('name')
            ->add('email')
            ->add('position')
            ;
    }
}