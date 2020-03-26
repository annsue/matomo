<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PagePerformance;

use Piwik\Archive;
use Piwik\Piwik;
use Piwik\Plugin\ProcessedMetric;
use Piwik\Plugins\PagePerformance\Columns\Metrics\AveragePageLoadTime;
use Piwik\Plugins\PagePerformance\Columns\Metrics\AverageTimeDomCompletion;
use Piwik\Plugins\PagePerformance\Columns\Metrics\AverageTimeDomProcessing;
use Piwik\Plugins\PagePerformance\Columns\Metrics\AverageTimeLatency;
use Piwik\Plugins\PagePerformance\Columns\Metrics\AverageTimeOnLoad;
use Piwik\Plugins\PagePerformance\Columns\Metrics\AverageTimeTransfer;

/**
 * @method static \Piwik\Plugins\PagePerformance\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    public function get($idSite, $period, $date, $segment = false)
    {
        Piwik::checkUserHasViewAccess($idSite);

        $archive = Archive::build($idSite, $period, $date, $segment);

        $columns = array(
            Archiver::PAGEPERFORMANCE_TOTAL_LATENCY_TIME,
            Archiver::PAGEPERFORMANCE_TOTAL_LATENCY_HITS,
            Archiver::PAGEPERFORMANCE_TOTAL_TRANSFER_TIME,
            Archiver::PAGEPERFORMANCE_TOTAL_TRANSFER_HITS,
            Archiver::PAGEPERFORMANCE_TOTAL_DOMPROCESSING_TIME,
            Archiver::PAGEPERFORMANCE_TOTAL_DOMPROCESSING_HITS,
            Archiver::PAGEPERFORMANCE_TOTAL_DOMCOMPLETION_TIME,
            Archiver::PAGEPERFORMANCE_TOTAL_DOMCOMPLETION_HITS,
            Archiver::PAGEPERFORMANCE_TOTAL_ONLOAD_TIME,
            Archiver::PAGEPERFORMANCE_TOTAL_ONLOAD_HITS,
            Archiver::PAGEPERFORMANCE_TOTAL_PAGE_LOAD_TIME,
            Archiver::PAGEPERFORMANCE_TOTAL_PAGE_LOAD_HITS,
        );

        $dataTable = $archive->getDataTableFromNumeric($columns);

        $precision = 2;

        $dataTable->filter('ColumnCallbackAddColumnQuotient', array(
            $this->getMetricColumn(AverageTimeLatency::class),
            Archiver::PAGEPERFORMANCE_TOTAL_LATENCY_TIME,
            Archiver::PAGEPERFORMANCE_TOTAL_LATENCY_HITS,
            $precision
        ));

        $dataTable->filter('ColumnCallbackAddColumnQuotient', array(
            $this->getMetricColumn(AverageTimeTransfer::class),
            Archiver::PAGEPERFORMANCE_TOTAL_TRANSFER_TIME,
            Archiver::PAGEPERFORMANCE_TOTAL_LATENCY_HITS,
            $precision
        ));

        $dataTable->filter('ColumnCallbackAddColumnQuotient', array(
            $this->getMetricColumn(AverageTimeDomProcessing::class),
            Archiver::PAGEPERFORMANCE_TOTAL_DOMPROCESSING_TIME,
            Archiver::PAGEPERFORMANCE_TOTAL_DOMPROCESSING_HITS,
            $precision
        ));

        $dataTable->filter('ColumnCallbackAddColumnQuotient', array(
            $this->getMetricColumn(AverageTimeDomCompletion::class),
            Archiver::PAGEPERFORMANCE_TOTAL_DOMCOMPLETION_TIME,
            Archiver::PAGEPERFORMANCE_TOTAL_DOMCOMPLETION_HITS,
            $precision
        ));

        $dataTable->filter('ColumnCallbackAddColumnQuotient', array(
            $this->getMetricColumn(AverageTimeOnLoad::class),
            Archiver::PAGEPERFORMANCE_TOTAL_ONLOAD_TIME,
            Archiver::PAGEPERFORMANCE_TOTAL_ONLOAD_HITS,
            $precision
        ));

        $dataTable->filter('ColumnCallbackAddColumnQuotient', array(
            $this->getMetricColumn(AveragePageLoadTime::class),
            Archiver::PAGEPERFORMANCE_TOTAL_PAGE_LOAD_TIME,
            Archiver::PAGEPERFORMANCE_TOTAL_PAGE_LOAD_HITS,
            $precision
        ));

        $dataTable->queueFilter('ColumnDelete', array($columns));

        return $dataTable;
    }

    /**
     * @param string $class
     * @return string
     */
    private function getMetricColumn($class) {
        /** @var ProcessedMetric $metric */
        $metric = new $class();
        return $metric->getName();
    }
}
