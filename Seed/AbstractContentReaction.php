<?php

namespace TickTackk\Seeder\Seed;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure as EntityStructure;
use XF\Phrase;
use XF\Repository\Reaction as ReactionRepo;
use XF\Entity\Reaction as ReactionEntity;
use XF\Entity\ReactionContent as ReactionContentEntity;

abstract class AbstractContentReaction extends AbstractSeed
{
    abstract protected function getEntityShortName() : string;

    abstract protected function getUserIdColumn() : string;

    abstract protected function getReactionRelationName() : string;

    protected function getStructure() : EntityStructure
    {
        return $this->app->em()->getEntityStructure($this->getEntityShortName());
    }

    public function getTitle() : Phrase
    {
        $structure = $this->getStructure();
        $contentTypePlural = $this->app->getContentTypePhrase($structure->contentType, true);

        return \XF::phrase('tckSeeder_adding_reactions_to_x', [
            'content_type_plural' => $contentTypePlural
        ]);
    }

    public function getReaction(Entity $content) :? ReactionEntity
    {
        /** @var ReactionContentEntity[] $reactionContents */
        $reactionContents = $content->getRelation($this->getReactionRelationName());
        $reactionIds = [];

        foreach ($reactionContents AS $reactionContent)
        {
            $reactionIds[] = $reactionContent->reaction_id;
        }

        return $this->finderWithRandomOrder('XF:Reaction')
            ->where('reaction_id', '<>', $reactionIds)
            ->fetchOne();
    }

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

    protected function seed(array $params = []): bool
    {
        $randomEntity = $this->app->find($this->getEntityShortName(), $this->getRandomContentId());
        if (!$randomEntity)
        {
            return false;
        }

        $reaction = $this->getReaction($randomEntity);
        if (!$reaction)
        {
            return false;
        }

        $reactionRepo = $this->getReactionRepo();
        $reactionContent = $reactionRepo->reactToContent($reaction->reaction_id,
            $randomEntity->getEntityContentType(),
            $randomEntity->getExistingEntityId(),
            \XF::visitor(),
            $this->faker()->boolean
        );

        return $reactionContent ? true : false;
    }

    protected function getReactionRepo() : ReactionRepo
    {
        return $this->app->repository('XF:Reaction');
    }
}