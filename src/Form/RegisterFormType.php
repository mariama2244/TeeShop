<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                   new NotBlank([
                    'message' => 'Ce champ ne peut etre vide : {{ value }}'
                   ]),
                   new Length([
                       'min' => 6,
                       'max' => 180,
                       'minMessage' => 'Votre email doit comporter au minimum {{ limit }} caractères. (email : {{ value }}',
                       'maxMessage' => 'Votre email doit comporter au minimum {{ limit }} caractères.'
                   ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut etre vide : {{ value }}'
                    ]),
                    new Length([
                        'min' => 4,
                        'max' => 255,
                        'minMessage' => 'La valeur doit comporter au minimum {{ limit }} caractères.',
                        'maxMessage' => 'La valeur doit comporter au minimum {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut etre vide : {{ value }}'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'La valeur doit comporter au minimum {{ limit }} caractères.',
                        'maxMessage' => 'La valeur doit comporter au minimum {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut etre vide : {{ value }}'
                    ]),
                    new Length([
                        'min' => 1,
                        'max' => 100,
                        'minMessage' => 'La valeur doit comporter au minimum {{ limit }} caractères.',
                        'maxMessage' => 'La valeur doit comporter au minimum {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Civilité',
                'choices' => [
                    'Homme' => 'home',
                    'Femme' => 'femme',
                    'Non binaire' => 'non-binaire'
                ],
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
                'choice_attr' => [
                    'class' => 'radio-inline'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut etre vide : {{ value }}'
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'validate' => false,
                'attr' => [
                    'class' => 'd-block mx-auto col-3 btn btn-warning'
                ]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
