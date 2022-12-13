# adventofcode


## Install

```bash
$ composer install
```

## Execute algo
```bash
Use    : bin/console algo [OPTION]...
OPTIONS:
          --help                        Reserved - Display Help
          --color                       Output with colors
  -y ARG, --year=ARG                    Year to solve (default current year)
  -d ARG, --day=ARG                     Day to solve (default current day)
  -e,     --example                     Example Only
  -s,     --skip-empty-lines            Remove empty lines from inputs
          --render                      Use render script instead of solver script (if present)

```

Example:
```bash
$ bin/console --color algo --year=2020 --day=6   
 *** RUN - Algo - 2021-11-21 01:19:16 ***
------------------------------------------ EXAMPLES ------------------------------------------
*:  11 - expected: 11
**:  6 - expected: 6
------------------------------------------- OUTPUT -------------------------------------------
*  : 6726
** : 3316
 *** END SCRIPT - Time taken: 0s - 2021-11-21 01:19:16 ***

```
