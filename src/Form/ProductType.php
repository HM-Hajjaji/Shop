<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class)
            ->add('description',TextareaType::class)
            ->add('price',MoneyType::class)
            ->add('discount',PercentType::class,['required' => false])
            ->add('quantity',IntegerType::class,[
                'attr' => ['min' => 0]
            ])
            ->add('photo',FileType::class,[
                'mapped' => false,
                'attr'  => ['accept' => 'image/*'],
                'required' => $options['photo_require']
                /*'constraints' => [
                    new File([
                        'maxSize' => '2024k',
                        'mimeTypesMessage' => 'Please upload a valid Photo',
                    ])
                ],*/
            ])
            ->add('category',EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'name',
                ])
            ->add('submit',SubmitType::class,['label' => $options['btn_name']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'btn_name' => 'Submit',
            'photo_require' => true
        ]);
        $resolver->setAllowedTypes('btn_name','string');
    }
}
