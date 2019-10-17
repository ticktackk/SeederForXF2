<?php

namespace TickTackk\Seeder\Seed;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Repository;
use XF\Mvc\Entity\Structure as EntityStructure;
use XF\Phrase;
use XF\Repository\Reaction as ReactionRepo;
use XF\Entity\Reaction as ReactionEntity;
use XF\Entity\ReactionContent as ReactionContentEntity;

/**
 * Class AbstractReactionContent
 *
 * @package TickTackk\Seeder\Seed
 */
abstract class AbstractReactionContent extends AbstractSeed
{
    /**
     * @return string
     */
    abstract protected function getEntityShortName() : string;

    /**
     * @return string
     */
    abstract protected function getUserIdColumn() : string;

    /**
     * @return string
     */
    abstract protected function getReactionRelationName() : string;

    /**
     * @return EntityStructure
     */
    protected function getStructure() : EntityStructure
    {
        return $this->app->em()->getEntityStructure($this->getEntityShortName());
    }

    /**
     * @return Phrase
     */
    public function getTitle() : Phrase
    {
        $structure = $this->getStructure();
        $contentTypePlural = $this->app->getContentTypePhrase($structure->contentType, true);

        return \XF::phrase('tckSeeder_adding_reactions_to_x', [
            'content_type_plural' => $contentTypePlural
        ]);
    }

    /**
     * @param Entity $content
     *
     * @return null|Entity|ReactionEntity
     */
    public function getReaction(Entity $content) :? ReactionEntity
    {
        /** @var ReactionContentEntity[] $reactionContents */
        $reactionContents = $content->getRelation($this->getReactionRelationName());
        $reactionIds = [];

        foreach ($reactionContents AS $reactionContent)
        {
            $reactionIds[] = $reactionContent->reaction_id;
        }

        return $this->randomEntity('XF:Reaction', [['reaction_id', '<>', $reactionIds]]);
    }

    /**
     * @return int
     */
    protected function getRandomContentId() : int
    {
        $visitor = \XF::visitor();
        $structure = $this->getStructure();
        $table = $structure->table;
        $tableAlias = utf8_substr($table, 3);
        $primaryKey = $structure->primaryKey;
        $totalReactions = (int) $this->finder('XF:Reaction')->total();
        $userId = $this->getUserIdColumn();

        return (int) $this->app->db()->fetchOne(
            "
                SELECT {$primaryKey}
                FROM {$table} AS {$tableAlias}
                LEFT JOIN xf_reaction_content AS reaction_content
                    ON (reaction_content.content_id = {$tableAlias}.{$primaryKey} AND reaction_content.content_type = ?)
                WHERE {$tableAlias}.{$userId} <> ?
                GROUP BY {$tableAlias}.{$primaryKey}
                HAVING COUNT(reaction_content.reaction_id) < ?
                ORDER BY RAND()
                LIMIT 1
        ", [$structure->contentType, $visitor->user_id, $totalReactions]);
    }

    /**
     * @param array|null $errors
     */
    protected function _seed(array &$errors = null) : void
    {
        $visitor = \XF::visitor();
        $randomEntity = $this->app->find($this->getEntityShortName(), $this->getRandomContentId());

        if ($randomEntity)
        {
            $reaction = $this->getReaction($randomEntity);
            if ($reaction)
            {
                $reactionRepo = $this->getReactionRepo();
                $reactionRepo->reactToContent($reaction->reaction_id,
                    $randomEntity->getEntityContentType(),
                    $randomEntity->getExistingEntityId(),
                    $visitor,
                    $this->faker()->boolean
                );
            }
        }
    }

    /**
     * @return Repository|ReactionRepo
     */
    protected function getReactionRepo() : ReactionRepo
    {
        return $this->app->repository('XF:Reaction');
    }
}