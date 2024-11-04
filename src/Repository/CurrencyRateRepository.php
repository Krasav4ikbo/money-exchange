<?php

namespace App\Repository;

use App\DTO\CurrenciesUpdateDTO;
use App\DTO\CurrencyUpdateDTO;
use App\DTO\ExchangeInputDTO;
use App\Entity\CurrencyRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends CurrencyRateRepository<CurrencyRate>
 */
class CurrencyRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyRate::class);
    }

    public function findRate(ExchangeInputDTO $exchange)
    {
        return $this->createQueryBuilder('cr')
            ->where('cr.provider=:provider')
            ->andWhere('(cr.iso_from = :iso_from and cr.iso_to = :iso_to) or (cr.iso_from = :iso_to and cr.iso_to = :iso_from)')
            ->setParameter('iso_from', $exchange->getIsoFrom())
            ->setParameter('iso_to', $exchange->getIsoTo())
            ->setParameter('provider', $exchange->getAppSource())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCrossRates(ExchangeInputDTO $exchange)
    {
        $qb = $this->createQueryBuilder('cr');

        $currenciesList = [$exchange->getIsoFrom(), $exchange->getIsoTo()];

        return $qb
            ->select('cr', 'crj')
            ->innerJoin(CurrencyRate::class, 'crj')
            ->where('cr.provider=:provider')
            ->andWhere('crj.provider=:provider')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->in('cr.iso_from', 'crj.iso_to'),
                    $qb->expr()->in('crj.iso_from', 'cr.iso_to'),
                ),
                $qb->expr()->andX(
                    $qb->expr()->in('cr.iso_from', 'crj.iso_from'),
                    $qb->expr()->in('cr.iso_to', 'crj.iso_to'),
                ),
            ))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->in('cr.iso_from', $currenciesList),
                    $qb->expr()->in('crj.iso_to', $currenciesList),
                ),
                $qb->expr()->andX(
                    $qb->expr()->in('cr.iso_from', $currenciesList),
                    $qb->expr()->in('crj.iso_from', $currenciesList),
                ),
                $qb->expr()->andX(
                    $qb->expr()->in('cr.iso_to', $currenciesList),
                    $qb->expr()->in('crj.iso_to', $currenciesList),
                ),
                $qb->expr()->andX(
                    $qb->expr()->in('cr.iso_to', $currenciesList),
                    $qb->expr()->in('crj.iso_from', $currenciesList),
                )
            ))
            ->setParameter('provider', $exchange->getAppSource())
            ->getQuery()
            ->getResult();
    }

    public function findAllRatesBySource(string $source)
    {
        return $this->createQueryBuilder('c')
            ->where('c.provider = :provider')
            ->setParameter('provider', $source)
            ->getQuery()
            ->getResult();
    }

    public function updateCurrencyRate(CurrenciesUpdateDTO $currenciesUpdateDTO, string $provider): void
    {
        $allRatesBySource = $this->findAllRatesBySource($provider);

        $ratesByCodes = [];

        /** @var $currenciesUpdateDTO CurrenciesUpdateDTO */
        foreach ($currenciesUpdateDTO->getCurrenciesUpdate() as $currencyUpdate) {
            $ratesByCodes[$currencyUpdate->getSlug()]= $currencyUpdate;
        }

        /** @var $currencyRate CurrencyRate */
        foreach ($allRatesBySource as $currencyRate) {
            if (!empty($ratesByCodes[$currencyRate->getSlug()])) {
                $this->convertToModel($ratesByCodes[$currencyRate->getSlug()], $currencyRate);
                unset($ratesByCodes[$currencyRate->getSlug()]);
                unset($currencyRate);
            } else {
                // remove not supported rates
                $this->getEntityManager()->remove($currencyRate);
            }
        }

        foreach ($ratesByCodes as $rateByCode) {
            $model = $this->convertToModel($rateByCode);
            $this->getEntityManager()->persist($model);
        }

        $this->getEntityManager()->flush();
    }

    private function convertToModel(
        CurrencyUpdateDTO $currencyUpdateDTO,
        ?CurrencyRate $currencyRate = null
    ): CurrencyRate
    {
        if (!$currencyRate) {
            $currencyRate = new CurrencyRate();
        }

        return $currencyRate
            ->setIsoFrom($currencyUpdateDTO->getIsoFrom())
            ->setIsoTo($currencyUpdateDTO->getIsoTo())
            ->setRate($currencyUpdateDTO->getRate())
            ->setProvider($currencyUpdateDTO->getProvider())
            ->setInvertedRate($currencyUpdateDTO->getInvertedRate())
            ->setNominal($currencyUpdateDTO->getNominal());
    }
}
