#!/bin/bash

if (( $# != 2 || $1 < 8 || $2 < ($1 / 2 * 2) - 1 || $2 > ($1 / 2 * 2) )); then
    echo "You can't build a tree with such parameters. Please check if tree hight is not less than 8 and the width is an odd number" >&2
    exit 1
fi

TREE_H=$(( $1 / 2 * 2 ))
TREE_W=$(( TREE_H - 1 ))

generate_branches() {
    local level_count=$(( (TREE_H - 2) / 2 ))
    local max_line_width=$(( TREE_W - 2 ))
    local mark="*"

    for ((line_idx = 1; line_idx <= level_count; line_idx++)); do
        local chars=$(( 1 + (line_idx - 1) * 2 ))
        local left_pad=$(( ((max_line_width - chars) / 2) + 1 ))

        printf "%${left_pad}s" ""
        printf "%${chars}s\n" | tr ' ' "$mark"

        if [[ "$mark" == "*" ]]; then
            mark="#"
        else
            mark="*"
        fi
    done

    line_idx=2
    until [[ $line_idx -gt level_count ]]; do
        local chars=$(( 1 + (line_idx - 1) * 2 ))
        local left_pad=$(( ((max_line_width - chars) / 2) + 1 ))

        printf "%${left_pad}s" ""
        printf "%${chars}s\n" | tr ' ' "$mark"

        if [[ "$mark" == "*" ]]; then
            mark="#"
        else
            mark="*"
        fi
        ((line_idx++))
    done
}

generate_branches $(((TREE_H - 1) / 2)) $((TREE_W - 2))

base_pad=$(( (TREE_W - 3) / 2 ))

for stem in 1 2; do
    printf "%${base_pad}s###\n"
done

star_idx=0
while [[ $star_idx -lt $TREE_W ]]; do
    printf "*"
    ((star_idx++))
done
printf "\n"

