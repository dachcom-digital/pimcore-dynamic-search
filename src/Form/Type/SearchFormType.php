<?php

namespace DynamicSearchBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SearchFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'csrf_protection'   => false,
            'validation_groups' => false,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('q', SearchType::class, [
            'label' => false,
            'attr'  => [
                'class'        => 'form-control form-control-lg',
                'placeholder'  => 'dynamic_search.form.placeholder',
                'autocomplete' => 'off'
            ]
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'dynamic_search.form.search',
            'attr'  => [
                'class' => 'btn btn-primary'
            ]
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'dynamic_search_form';
    }
}
