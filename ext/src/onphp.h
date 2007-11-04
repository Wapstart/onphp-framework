/***************************************************************************
 *   Copyright (C) 2006-2007 by Konstantin V. Arkhipov                     *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/
/* $Id$ */

#ifndef ONPHP_H
#define ONPHP_H

#include "php.h"
#include "zend_interfaces.h"

#define ONPHP_VERSION "0.11.1.111"
#define ONPHP_MODULE_NAME "onPHP"

#define ZVAL_FREE(z) zval_dtor(z); FREE_ZVAL(z);

#define ONPHP_INSTANCEOF(object, class_name) \
	( \
		(Z_TYPE_P(object) == IS_OBJECT) \
		&& instanceof_function(Z_OBJCE_P(object), onphp_ce_ ## class_name TSRMLS_CC) \
	)

#define ONPHP_GET_ARGS(type_spec, ...) \
	if ( \
		zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, type_spec, __VA_ARGS__) == FAILURE \
	) { \
		WRONG_PARAM_COUNT; \
	}

#define ONPHP_CALL_METHOD_0(object, method_name, out) \
	zend_call_method_with_0_params(&object, Z_OBJCE_P(object), NULL, method_name, &out); \
	if (EG(exception)) return

#define ONPHP_CALL_METHOD_1(object, method_name, out, first_argument) \
	zend_call_method_with_1_params(&object, Z_OBJCE_P(object), NULL, method_name, &out, first_argument); \
	if (EG(exception)) return

#define ONPHP_CALL_METHOD_2(object, method_name, out, first_argument, second_argument) \
	zend_call_method_with_2_params(&object, Z_OBJCE_P(object), NULL, method_name, &out, first_argument, second_argument); \
	if (EG(exception)) return

#define ONPHP_ME(class_name, function_name, arg_info, flags) \
	PHP_ME(onphp_ ## class_name, function_name, arg_info, flags)

#define ONPHP_ABSTRACT_ME(class_name, function_name, arg_info, flags) \
	ZEND_FENTRY(function_name, NULL, arg_info, flags|ZEND_ACC_ABSTRACT)

#define REGISTER_ONPHP_INTERFACE(class_name) \
	spl_register_interface(&onphp_ce_ ## class_name, # class_name, onphp_funcs_ ## class_name TSRMLS_CC);

#define REGISTER_ONPHP_IMPLEMENTS(class_name, interface_name) \
	zend_class_implements(onphp_ce_ ## class_name TSRMLS_CC, 1, onphp_ce_ ## interface_name);

#define REGISTER_ONPHP_STD_CLASS(class_name, obj_ctor) \
	spl_register_std_class(&onphp_ce_ ## class_name, # class_name, obj_ctor, NULL TSRMLS_CC);

#define REGISTER_ONPHP_STD_CLASS_EX(class_name) \
	spl_register_std_class(&onphp_ce_ ## class_name, # class_name, onphp_empty_object_new, onphp_funcs_ ## class_name TSRMLS_CC);

#define REGISTER_ONPHP_SUB_CLASS_EX(class_name, parent_class_name) \
	spl_register_sub_class(&onphp_ce_ ## class_name, onphp_ce_ ## parent_class_name, # class_name, onphp_empty_object_new, onphp_funcs_ ## class_name TSRMLS_CC);

#define REGISTER_ONPHP_CUSTOM_SUB_CLASS_EX(class_name, parent_class_name, obj_ctor, funcs) \
	spl_register_sub_class(&onphp_ce_ ## class_name, onphp_ce_ ## parent_class_name, # class_name, obj_ctor, funcs TSRMLS_CC);

#define REGISTER_ONPHP_PROPERTY(class_name, prop_name, prop_flags) \
	zend_declare_property_null(onphp_ce_ ## class_name, prop_name, strlen(prop_name), prop_flags TSRMLS_CC);

#define REGISTER_ONPHP_PROPERTY_BOOL(class_name, prop_name, bool, prop_flags) \
	zend_declare_property_bool(onphp_ce_ ## class_name, prop_name, strlen(prop_name), bool, prop_flags TSRMLS_CC);

#define REGISTER_ONPHP_CLASS_CONST_LONG(class_name, const_name, value) \
	zend_declare_class_constant_long(onphp_ce_ ## class_name, const_name, strlen(const_name), (long) value TSRMLS_CC);

#define ONPHP_READ_PROPERTY(class, property) \
	zend_read_property(Z_OBJCE_P(class), class, property, strlen(property), 1 TSRMLS_CC)

#define ONPHP_UPDATE_PROPERTY(class, property, value) \
	zend_update_property(Z_OBJCE_P(class), class, property, strlen(property), value TSRMLS_CC)

#define ONPHP_UPDATE_PROPERTY_BOOL(class, property, value) \
	zend_update_property_bool(Z_OBJCE_P(class), class, property, strlen(property), value TSRMLS_CC)

#define ONPHP_UPDATE_PROPERTY_LONG(class, property, value) \
	zend_update_property_long(Z_OBJCE_P(class), class, property, strlen(property), value TSRMLS_CC)

#define ONPHP_METHOD(class_name, function_name) \
	PHP_METHOD(onphp_ ## class_name, function_name)

#define ONPHP_GETTER(class_name, method_name, property_name) \
	ONPHP_METHOD(class_name, method_name) \
	{ \
		zval *property_name = ONPHP_READ_PROPERTY(getThis(), # property_name); \
		RETURN_ZVAL(property_name, 1, 0); \
	}

#define ONPHP_SETTER(class_name, method_name, property_name) \
	ONPHP_METHOD(class_name, method_name) \
	{ \
		zval *property_name; \
		\
		ONPHP_GET_ARGS("z", &property_name) \
		\
		ONPHP_UPDATE_PROPERTY(getThis(), # property_name, property_name); \
		\
		RETURN_ZVAL(getThis(), 1, 0); \
	}

#define ONPHP_MAKE_OBJECT(class_name, zval) \
	MAKE_STD_ZVAL(zval); \
	zval->value.obj = onphp_empty_object_new(onphp_ce_ ## class_name TSRMLS_CC); \
	Z_TYPE_P(zval) = IS_OBJECT;

#define ONPHP_STANDART_CLASS(class_name) \
	PHPAPI zend_class_entry *onphp_ce_ ## class_name; \
	extern zend_function_entry onphp_funcs_ ## class_name[];

#define ONPHP_ARGINFO_ONE \
	ZEND_BEGIN_ARG_INFO(arginfo_one, 0) \
		ZEND_ARG_INFO(0, first) \
	ZEND_END_ARG_INFO()

#define ONPHP_ARGINFO_ONE_REF \
	ZEND_BEGIN_ARG_INFO(arginfo_one_ref, 0) \
		ZEND_ARG_INFO(1, value) \
	ZEND_END_ARG_INFO() \

#define ONPHP_ARGINFO_TWO \
	ZEND_BEGIN_ARG_INFO(arginfo_two, 0) \
		ZEND_ARG_INFO(0, first) \
		ZEND_ARG_INFO(0, second) \
	ZEND_END_ARG_INFO()

#define ONPHP_ARGINFO_THREE \
	ZEND_BEGIN_ARG_INFO(arginfo_three, 0) \
		ZEND_ARG_INFO(0, first) \
		ZEND_ARG_INFO(0, second) \
		ZEND_ARG_INFO(0, third) \
	ZEND_END_ARG_INFO()

#define onphp_empty_object zend_object

extern void onphp_empty_object_free_storage(void *object TSRMLS_DC);
extern zend_object_value onphp_empty_object_spawn(
	zend_class_entry *class_type,
	onphp_empty_object **object TSRMLS_DC
);
extern zend_object_value onphp_empty_object_new(
	zend_class_entry *class_type TSRMLS_DC
);

#endif /* ONPHP_H */
