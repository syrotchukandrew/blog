<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;



class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'attr' => array('autofocus' => true,),
                'label' => 'Назва посту',
            ))
            ->add('content', TextareaType::class, array(
                'label' => 'Введіть опис посту',
                'attr' => [
                    'placeholder' => 'Введіть текст посту',
                    'class' => 'form-control',
                    'rows' => 5
                ]
            ))
            ->add('videoId', TextType::class, array(
                'label' => 'Відео з YouTube',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Вставте ID свого відео на YouTube',
                ]
            ))
            ->add('image_file', FileType::class, array(
                'label' => 'Завантажити фото',
                'required' => false
            ))
            ->add('tags', EntityType::class, array(
                'label' => 'Виберіть тег для цього посту (якщо потрібний тег відсутній - потрібно спочатку його створити)',
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
