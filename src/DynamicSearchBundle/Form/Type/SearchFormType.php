<?php

namespace DynamicSearchBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SearchFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'csrf_protection'   => false,
            'validation_groups' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('q', SearchType::class, [
            'label' => false,
            'attr'  => [
                'class'       => 'form-control form-control-lg',
                'placeholder' => 'dynamic_search.form.placeholder'
            ]
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'dynamic_search.form.search',
            'attr'  => [
                'class' => 'btn btn-primary'
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dynamic_search_form';
    }
}
