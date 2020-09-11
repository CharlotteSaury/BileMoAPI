<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('screen')
            ->add('das')
            ->add('weight')
            ->add('length')
            ->add('width')
            ->add('height')
            ->add('wifi')
            ->add('video4k')
            ->add('bluetooth')
            ->add('lte4G')
            ->add('camera')
            ->add('nfc')
            ->add('manufacturer')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'csrf_protection' => false
        ]);
    }
}
