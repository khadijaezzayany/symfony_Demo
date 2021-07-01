<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Application\Sonata\UserBundle\Admin\Model;

use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DatePickerType;
use Sonata\UserBundle\Form\Type\SecurityRolesType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Sonata\UserBundle\Admin\Model\UserAdmin as BaseAdmin;

class UserAdmin extends BaseAdmin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * {@inheritdoc}
     */
    public function getFormBuilder()
    {
        $this->formOptions['data_class'] = $this->getClass();

        $options = $this->formOptions;
        $options['validation_groups'] = [];

        // if (!$this->getSubject() || null === $this->getSubject()->getId()) {
        //     $options['validation_groups'] = ['Default', 'Registration'];
        // }

        $formBuilder = $this->getFormContractor()->getFormBuilder($this->getUniqid(), $options);

        $this->defineFormBuilder($formBuilder);

        return $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($user): void
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }

    public function setUserManager(UserManagerInterface $userManager): void
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('username',null,[
                'required' => false,
                'label' => "Email"
            ])
            ->add('groups')
            ->add('enabled', null, ['editable' => true])
            ->add('createdAt')
        ;

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add('impersonating', 'string', ['template' => '@SonataUser/Admin/Field/impersonating.html.twig'])
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper): void
    {
        $filterMapper
            ->add('id')
            ->add('username',null,[
                'required' => false,
                'label' => "Email"
            ])
            ->add('groups')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('Utilisateur')
                ->with('Profile', ['class' => 'col-md-6'])->end()
                ->with('General', ['class' => 'col-md-6'])->end()
            ->end()
            ->tab('Security')
                ->with('Status', ['class' => 'col-md-4'])->end()
                ->with('Groups', ['class' => 'col-md-4'])->end()
                ->with('Keys', ['class' => 'col-md-4'])->end()
                ->with('Roles', ['class' => 'col-md-12'])->end()
            ->end()
        ;

        $now = new \DateTime();

        $genderOptions = [
            'choices' => \call_user_func([$this->getUserManager()->getClass(), 'getGenderList']),
            'required' => true,
            'translation_domain' => $this->getTranslationDomain(),
        ];

        $formMapper
            ->tab('Utilisateur')
                ->with('General')
                    ->add('username',EmailType::class,[
                        'required' => true,
                        'label' => "Email"
                    ])
                    ->add('plainPassword', TextType::class, [
                        'required' => (!$this->getSubject() || null === $this->getSubject()->getId()),
                    ])
                ->end()
                ->with('Profile')
                    ->add('firstname', null, [
                        'required' => true,
                        'label' => "Prénom"
                    ])
                    ->add('lastname', null, [
                        'required' => true,
                        'label' => "Nom"]
                    )
                    ->add('phone', null, [
                        'required' => false,
                        'label' => "Numéro de téléphone"
                    ])
                    ->add('societe', null, [
                        'required' => false,
                        'label' => "Societe"
                    ])
                    ->add('siret', null, [
                        'required' => false,
                        'label' => "Siret"
                    ])
                    ->add('adresse', null, [
                        'required' => true,
                        'label' => "Adresse"
                    ])
                    ->add('code_postal', null, [
                        'required' => true,
                        'label' => "Code postal"
                    ])
                    ->add('ville', null, [
                        'required' => true,
                        'label' => "Ville"
                    ])
                    ->add('pays', null, [
                        'required' => true,
                        'label' => "Pays"
                    ])
                ->end()
            ->end()
            ->tab('Security')
                ->with('Status')
                    ->add('enabled', null, ['required' => false, 'label' => 'Statut'])
                ->end()
                ->with('Groups')
                    ->add('groups', ModelType::class, [
                        'required' => false,
                        'expanded' => true,
                        'multiple' => true,
                        'btn_add' => false
                    ])
                ->end()
                ->with('Roles')
                    ->add('realRoles', SecurityRolesType::class, [
                        'label' => 'form.label_roles',
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false,
                    ])
                ->end()
                ->with('Keys')
                    ->add('token', null, ['required' => false])
                    ->add('twoStepVerificationCode', null, ['required' => false])
                ->end()
            ->end()
        ;
    }

    protected function configureExportFields(): array
    {
        // Avoid sensitive properties to be exported.
        return array_filter(parent::configureExportFields(), static function (string $v): bool {
            return !\in_array($v, ['password', 'salt'], true);
        });
    }
    public function prePersist($object){
        $object->setEmail($object->getUsername());
    }
}
