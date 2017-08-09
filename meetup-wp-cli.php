<?php
/**
 * Plugin Name: Meetup CLI
 * Description: Adds meetup commands to WP CLI
 * Version: 0.1
 * Author: Jake Chamberlain
 * Author URI: http://jchamb.com
 *
 */

if( defined( 'WP_CLI' ) && WP_CLI ) {

    class Meetup_WP_CLI extends WP_CLI_Command
    {
        protected $verbose = false;

        function helpers()
        {
            WP_CLI::log("Just some output");
            WP_CLI::error("Im an error");
            WP_CLI::success("I'm successful");
        }


        function args($args, $assoc_args)
        {
            WP_CLI::log( $args[0] );
            WP_CLI::log( $args[1] );

            WP_CLI::log( $assoc_args['verbose'] );
            WP_CLI::log( $assoc_args['option'] );
        }


        /**
         * Prints a greeting.
         *
         * ## OPTIONS
         *
         * <name>
         * : The name of the person to greet.
         *
         * [--type=<type>]
         * : Whether or not to greet the person with success or error.
         * ---
         * default: success
         * options:
         *   - success
         *   - error
         * ---
         *
         * ## EXAMPLES
         *
         *     wp example hello Newman
         *
         * @when after_wp_load
         * @alias hi
         */
        function hello( $args, $assoc_args )
        {
            list( $name ) = $args;

            // Print the message with type
            $type = $assoc_args['type'];
            WP_CLI::$type( "Hello, $name!" );
        }

        /**
         * @subcommand list
         */
        function _list( $args, $assoc_args )
        {
            WP_CLI::success( "_list" );
        }

        /**
         * @subcommand do-chores
         */
        function do_chores( $args, $assoc_args )
        {
            WP_CLI::success( "do_chores" );
        }


        /**
         * @subcommand user-input
         */
        function user_input($args, $assoc_args)
        {
            WP_CLI::confirm( "You positive brah??", $assoc_args );
            WP_CLI::success( "word" );
        }

        function tables()
        {
            $items = array(
                array(
                    'key'   => 'foo',
                    'value'  => 'bar',
                )
            );

            WP_CLI\Utils\format_items( 'table', $items, array( 'key', 'value' ) );
        }

        function progress($args, $assoc_args)
        {
            $count = $assoc_args['count'];

            $progress = \WP_CLI\Utils\make_progress_bar( 'Generating users', $count );
            for ( $i = 0; $i < $count; $i++ ) {
                sleep(1);
                $progress->tick();
            }
            $progress->finish();
        }

    }

    WP_CLI::add_command( 'meetup', 'Meetup_WP_CLI' );
}
