<?php

namespace Oro\Bundle\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\Common\Util\ClassUtils;

use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\EntityExtendBundle\Form\Type\EnumSelectType;
use Oro\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ProductBundle\Provider\CustomFieldProvider;
use Oro\Bundle\ProductBundle\Provider\ProductVariantAvailabilityProvider;

class FrontendVariantFiledType extends AbstractType
{
    const NAME = 'oro_product_frontend_variant_field';

    /** @var CustomFieldProvider */
    protected $customFieldProvider;

    /** @var ProductVariantAvailabilityProvider */
    protected $productVariantAvailabilityProvider;

    /** @var string */
    protected $productClass;

    /**
     * @param CustomFieldProvider $customFieldProvider
     * @param ProductVariantAvailabilityProvider $productVariantAvailabilityProvider
     * @param string $productClass
     */
    public function __construct(
        CustomFieldProvider $customFieldProvider,
        ProductVariantAvailabilityProvider $productVariantAvailabilityProvider,
        $productClass
    ) {
        $this->customFieldProvider = $customFieldProvider;
        $this->productVariantAvailabilityProvider = $productVariantAvailabilityProvider;
        $this->productClass = (string)$productClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'preSetData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'preSubmit']);
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $this->addVariantFields($event);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $this->addVariantFields($event);
    }

    /**
     * @param FormEvent $event
     */
    private function addVariantFields(FormEvent $event)
    {
        $data = $event->getData();

        if ($data === null) {
            return;
        }

        $form = $event->getForm();

        /** @var Product $product */
        $product = $form->getConfig()->getOption('product');

        if (!$product->isConfigurable() || count($product->getVariantFields()) === 0) {
            return;
        }

        $variantFieldData = $this->customFieldProvider->getEntityCustomFields($this->productClass);

        $variantAvailability = $this->productVariantAvailabilityProvider
            ->getVariantFieldsWithAvailability($product, $data);

        foreach ($product->getVariantFields() as $fieldName) {
            list($type, $fieldOptions) = $this->prepareFieldByType(
                $variantFieldData[$fieldName]['type'],
                $fieldName,
                $this->productClass,
                $variantAvailability[$fieldName]
            );
            $fieldOptions['label'] = $variantFieldData[$fieldName]['label'];

            $form->add($fieldName, $type, $fieldOptions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'product',
        ]);

        $resolver->addAllowedTypes('product', $this->productClass);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }

    /**
     * @param string $type
     * @param string $fieldName
     * @param string $class
     * @param array $availability
     * @return array
     */
    private function prepareFieldByType($type, $fieldName, $class, array $availability)
    {
        switch ($type) {
            case 'enum':
                $options = $this->getEnumOptions($class, $fieldName, $availability);

                return [EnumSelectType::NAME, $options];
            case 'boolean':
                $options = $this->getBooleanOptions($availability);

                return ['choice', $options];
            default:
                throw new \LogicException(
                    sprintf(
                        'Incorrect type. Expected "%s", but "%s" given',
                        implode('" or "', ['boolean', 'enum']),
                        $type
                    )
                );
        }
    }

    /**
     * @param string $class
     * @param string $fieldName
     * @param array $availability
     * @return array
     */
    private function getEnumOptions($class, $fieldName, array $availability)
    {
        $notAvailableVariants = array_filter($availability, function ($item) {
            return $item === false;
        });

        $disabledValues = array_keys($notAvailableVariants);

        return [
            'enum_code' => ExtendHelper::generateEnumCode($class, $fieldName),
            'configs' => ['allowClear' => false],
            // Next two lines required for selecting first element
            'required' => true,
            'placeholder' => false,
            'disabled_values' => $disabledValues,
        ];
    }

    /**
     * @param array $availability
     * @return array
     */
    private function getBooleanOptions(array $availability)
    {
        $availableVariants = array_filter($availability);

        $choiceAttrCallback = function ($val, $key, $index) use ($availableVariants) {
            $disabled = !array_key_exists($key, $availableVariants);

            return $disabled ? ['disabled' => 'disabled'] : [];
        };

        return [
            'choices' => ['No', 'Yes'],
            // Next two lines required for selecting first element
            'required' => true,
            'placeholder' => false,
            'choice_attr' => $choiceAttrCallback
        ];
    }
}