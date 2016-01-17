<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;



class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'attr' => array('autofocus' => true, 'rows' => 20, 'cols' => 112),
                'label' => 'Title',
            ))
            ->add('shortText', TextareaType::class, array(
                'attr' => [
                    'placeholder' => 'Add text article',
                    'class' => 'form-control',
                    'rows' => 5
                ]
            ))
            ->add('content', TextareaType::class, array(
                'attr' => [
                    'placeholder' => 'Add text article',
                    'class' => 'form-control',
                    'rows' => 5
                ]
            ))
            ->add('image_file', FileType::class, array(
                'required' => false
            ))
            ->add('tags', EntityType::class, array(
                'class' => 'AppBundle:Tag',
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true,))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Post',
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_post_type';
    }
}
