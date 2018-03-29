<?php declare(strict_types=1);

namespace Yokai\Versioning;

class ChangesetBuilder
{
    /**
     * Build a changeset
     *
     * @param array $oldSnapshot
     * @param array $newSnapshot
     *
     * @return array
     */
    public function build(array $oldSnapshot, array $newSnapshot): array
    {
        return $this->filterChangeset($this->mergeSnapshots($oldSnapshot, $newSnapshot));
    }

    /**
     * Merge the old and new snapshots
     *
     * @param array $oldSnapshot
     * @param array $newSnapshot
     *
     * @return array
     */
    private function mergeSnapshots(array $oldSnapshot, array $newSnapshot): array
    {
        $localNewSnapshot = array_map(
            function ($newItem) {
                return ['new' => $newItem];
            },
            $newSnapshot
        );

        $localOldSnapshot = array_map(
            function ($oldItem) {
                return ['old' => $oldItem];
            },
            $oldSnapshot
        );

        $mergedSnapshot = array_merge_recursive($localNewSnapshot, $localOldSnapshot);

        return array_map(
            function ($mergedItem) {
                return [
                    'old' => array_key_exists('old', $mergedItem) ? $mergedItem['old'] : '',
                    'new' => array_key_exists('new', $mergedItem) ? $mergedItem['new'] : '',
                ];
            },
            $mergedSnapshot
        );
    }

    /**
     * Filter changeSet to remove values that are the same
     *
     * @param array $changeset
     *
     * @return array
     */
    private function filterChangeset(array $changeset): array
    {
        return array_filter(
            $changeset,
            function ($item) {
                if ($item['old'] === '' && ($item['new'] === '' || $item['new'] === null)) {
                    return false;
                }

                if (is_float($item['old']) || is_float($item['new'])) {
                    $item['old'] = floatval($item['old']);
                    $item['new'] = floatval($item['new']);
                }

                return $item['old'] !== $item['new'];
            }
        );
    }
}
